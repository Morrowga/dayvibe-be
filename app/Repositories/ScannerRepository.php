<?php
namespace App\Repositories;

use Zxing\QrReader;
use App\Models\QROrder;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ScannerRepositoryInterface;

class ScannerRepository implements ScannerRepositoryInterface
{
    use CRUDResponses;

    public function index() {

    }

    public function store(Request $request) {
        try {
            // Determine the mode (upload or find)
            $mode = $request->input('mode', 'upload');

            if ($mode === 'find') {
                return $this->findByCode($request);
            } else {
                return $this->uploadAndProcess($request);
            }

        } catch (\Exception $e) {
            Log::error('QR Code processing error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error processing request: ' . $e->getMessage(),
                'errors' => ['general' => ['Error processing request: ' . $e->getMessage()]]
            ], 500);
        }
    }

    private function uploadAndProcess(Request $request)
    {
        try {
            $file = $request->file('qr_image');
            $path = $file->store('temp/qr-uploads', 'local');
            $fullPath = storage_path('app/' . $path);

            // Enhanced QR detection with multiple methods
            $qrText = $this->detectQRCodeWithMultipleMethods($fullPath);

            // Clean up the uploaded file
            Storage::disk('local')->delete($path);

            if (empty($qrText)) {
                return response()->json([
                    'message' => 'Could not detect or read QR code from the image',
                    'errors' => ['qr_image' => ['Could not detect or read QR code from the image']]
                ], 422);
            }

            // Handle Unicode characters and clean the JSON string
            $cleanedQrText = $this->cleanUnicodeJson($qrText);

            // Try to decode JSON
            $qrData = json_decode($cleanedQrText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error: ' . json_last_error_msg());
                Log::error('Original QR text: ' . $qrText);
                Log::error('Cleaned QR text: ' . $cleanedQrText);

                return response()->json([
                    'message' => 'QR code does not contain valid JSON data: ' . json_last_error_msg(),
                    'errors' => ['qr_image' => ['QR code does not contain valid JSON data']]
                ], 422);
            }

            $uniqueCode = $this->getNextAvailableCode();
            $itemArray = [];

            if (isset($qrData['items']) && is_array($qrData['items'])) {
                foreach($qrData['items'] as $item) {
                    $product = StoreProduct::find($item['id']);
                    if(!empty($product)) {
                        $itemArray[] = [
                            "id" => $product->id,
                            "img" => $product->first_image,
                            "quantity" => $item['q']
                        ];
                    }
                }
            }

            return response()->json([
                "data" => $qrData,
                "code" => $uniqueCode,
                "items" => $itemArray,
                "mode" => "upload"
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code upload processing error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error processing QR code: ' . $e->getMessage(),
                'errors' => ['qr_image' => ['Error processing QR code: ' . $e->getMessage()]]
            ], 500);
        }
    }

    private function cleanUnicodeJson($jsonString)
    {
        // Remove any BOM or extra quotes at the beginning/end
        $jsonString = trim($jsonString, '"');

        // Handle Unicode escape sequences properly
        $jsonString = preg_replace_callback('/\\\\x([0-9a-fA-F]{2})/', function($matches) {
            return chr(hexdec($matches[1]));
        }, $jsonString);

        // Handle other Unicode escape sequences
        $jsonString = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function($matches) {
            return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE');
        }, $jsonString);

        // Ensure proper UTF-8 encoding
        if (!mb_check_encoding($jsonString, 'UTF-8')) {
            $jsonString = mb_convert_encoding($jsonString, 'UTF-8', 'auto');
        }

        // Clean up any remaining problematic characters
        $jsonString = preg_replace('/[\x00-\x1F\x7F]/', '', $jsonString);

        // Try to fix common JSON issues
        $jsonString = str_replace(['\n', '\r', '\t'], ['', '', ''], $jsonString);

        return $jsonString;
    }

    private function parseUnicodeJson($jsonString)
    {
        // Try different approaches to parse the JSON
        $attempts = [
            // Method 1: Direct decode
            function($str) { return json_decode($str, true); },

            // Method 2: Clean and decode
            function($str) {
                $clean = $this->cleanUnicodeJson($str);
                return json_decode($clean, true);
            },

            // Method 3: Force UTF-8 and decode
            function($str) {
                $utf8 = mb_convert_encoding($str, 'UTF-8', 'auto');
                return json_decode($utf8, true);
            },

            // Method 4: Remove problematic characters
            function($str) {
                $clean = preg_replace('/[^\x20-\x7E\x{00A0}-\x{FFFF}]/u', '', $str);
                return json_decode($clean, true);
            },

            // Method 5: Parse manually if all else fails
            function($str) {
                return $this->manualJsonParse($str);
            }
        ];

        foreach ($attempts as $attempt) {
            try {
                $result = $attempt($jsonString);
                if ($result !== null && json_last_error() === JSON_ERROR_NONE) {
                    return $result;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Manual JSON parsing as last resort
     */
    private function manualJsonParse($jsonString)
    {
        // Extract items array manually
        if (preg_match('/"items":\s*\[(.*?)\]/', $jsonString, $matches)) {
            $itemsString = $matches[1];
            $items = [];

            // Parse each item manually
            preg_match_all('/\{"id":(\d+),"s":"[^"]*","q":(\d+)\}/', $itemsString, $itemMatches, PREG_SET_ORDER);

            foreach ($itemMatches as $match) {
                $items[] = [
                    'id' => (int)$match[1],
                    's' => '',
                    'q' => (int)$match[2]
                ];
            }

            // Extract other fields
            $result = ['items' => $items];

            if (preg_match('/"tq":(\d+)/', $jsonString, $tqMatch)) {
                $result['tq'] = (int)$tqMatch[1];
            }

            if (preg_match('/"ta":(\d+)/', $jsonString, $taMatch)) {
                $result['ta'] = (int)$taMatch[1];
            }

            if (preg_match('/"p":"([^"]+)"/', $jsonString, $pMatch)) {
                $result['p'] = $pMatch[1];
            }

            if (preg_match('/"c":"([^"]+)"/', $jsonString, $cMatch)) {
                $result['c'] = $cMatch[1];
            }

            if (preg_match('/"timestamp":(\d+)/', $jsonString, $timestampMatch)) {
                $result['timestamp'] = (int)$timestampMatch[1];
            }

            // For the name field, try to extract but handle Unicode
            if (preg_match('/"n":"([^"]*)"/', $jsonString, $nMatch)) {
                $result['n'] = $nMatch[1]; // Keep as is, might be empty or contain Unicode
            }

            return $result;
        }

        return null;
    }
    /**
     * Enhanced QR detection using multiple methods for maximum reliability
     */
    private function detectQRCodeWithMultipleMethods($imagePath)
    {
        $methods = [
            'detectQRWithLocalLibrary',
            'detectQRWithAPI1',
            'detectQRWithAPI2',
            'detectQRWithAPI3',
            'detectQRWithZXingImproved',
            'detectQRWithImagePreprocessing'
        ];

        foreach ($methods as $method) {
            try {
                $result = $this->$method($imagePath);
                if (!empty($result)) {
                    Log::info("QR detected successfully with method: {$method}");
                    return $result;
                }
            } catch (\Exception $e) {
                Log::warning("QR detection method {$method} failed: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    /**
     * Method 1: Local library (fastest)
     */
    private function detectQRWithLocalLibrary($imagePath)
    {
        try {
            $qrcode = new QrReader($imagePath);
            $text = $qrcode->text();
            return !empty($text) ? $text : null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method 2: QR Server API
     */
    private function detectQRWithAPI1($imagePath)
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($imagePath), basename($imagePath))
                ->post('https://api.qrserver.com/v1/read-qr-code/');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['symbol'][0]['data']) && !empty($data[0]['symbol'][0]['data'])) {
                    return $data[0]['symbol'][0]['data'];
                }
            }
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method 3: Base64 API approach
     */
    private function detectQRWithAPI2($imagePath)
    {
        try {
            $imageData = base64_encode(file_get_contents($imagePath));

            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://qr-code-reader-api.herokuapp.com/decode', [
                    'image' => $imageData,
                    'format' => 'base64'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['text'] ?? $data['data'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method 4: Alternative API
     */
    private function detectQRWithAPI3($imagePath)
    {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://qr-scanner-api.herokuapp.com/decode',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($imagePath)
                ],
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0 (compatible; QR-Scanner/1.0)'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200 && $response) {
                $data = json_decode($response, true);
                return $data['qr_code_text'] ?? $data['text'] ?? $data['data'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method 5: Improved ZXing detection
     */
    private function detectQRWithZXingImproved($imagePath)
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($imagePath), basename($imagePath))
                ->post('https://zxing.org/w/decode');

            if ($response->successful()) {
                $content = $response->body();

                // Multiple regex patterns to extract QR data
                $patterns = [
                    '/Raw text<\/td><td[^>]*>([^<]+)<\/td>/',
                    '/Raw text<\/td><td[^>]*><pre>([^<]+)<\/pre>/',
                    '/<pre[^>]*>([^<]+)<\/pre>/',
                    '/Parsed Result Type[^>]*>([^<]+)</',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $content, $matches)) {
                        $text = html_entity_decode(trim($matches[1]));
                        if (!empty($text)) {
                            return $text;
                        }
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method 6: Image preprocessing + detection
     */
    private function detectQRWithImagePreprocessing($imagePath)
    {
        try {
            // Create enhanced versions of the image
            $enhancedPaths = $this->preprocessImage($imagePath);

            foreach ($enhancedPaths as $enhancedPath) {
                try {
                    // Try local library on enhanced image
                    $qrcode = new QrReader($enhancedPath);
                    $text = $qrcode->text();

                    // Clean up enhanced image
                    if (file_exists($enhancedPath)) {
                        unlink($enhancedPath);
                    }

                    if (!empty($text)) {
                        return $text;
                    }
                } catch (\Exception $e) {
                    // Clean up on error
                    if (file_exists($enhancedPath)) {
                        unlink($enhancedPath);
                    }
                    continue;
                }
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Preprocess image to improve QR detection
     */
    private function preprocessImage($imagePath)
    {
        $enhancedPaths = [];
        $tempDir = storage_path('app/temp/');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        try {
            // Enhancement 1: Increase contrast and brightness
            $enhanced1 = $tempDir . 'enhanced1_' . basename($imagePath);
            $this->enhanceImage($imagePath, $enhanced1, 'contrast');
            $enhancedPaths[] = $enhanced1;

            // Enhancement 2: Convert to grayscale and threshold
            $enhanced2 = $tempDir . 'enhanced2_' . basename($imagePath);
            $this->enhanceImage($imagePath, $enhanced2, 'threshold');
            $enhancedPaths[] = $enhanced2;

            // Enhancement 3: Resize for better detection
            $enhanced3 = $tempDir . 'enhanced3_' . basename($imagePath);
            $this->enhanceImage($imagePath, $enhanced3, 'resize');
            $enhancedPaths[] = $enhanced3;

        } catch (\Exception $e) {
            Log::warning('Image preprocessing failed: ' . $e->getMessage());
        }

        return $enhancedPaths;
    }

    /**
     * Apply image enhancements using GD library
     */
    private function enhanceImage($sourcePath, $targetPath, $type)
    {
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        switch ($type) {
            case 'contrast':
                imagefilter($image, IMG_FILTER_CONTRAST, -50);
                imagefilter($image, IMG_FILTER_BRIGHTNESS, 30);
                break;

            case 'threshold':
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_CONTRAST, -100);
                break;

            case 'resize':
                $width = imagesx($image);
                $height = imagesy($image);
                $newWidth = $width * 2;
                $newHeight = $height * 2;

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
                break;
        }

        imagejpeg($image, $targetPath, 95);
        imagedestroy($image);
    }

    /**
     * Handle finding order by code number
     */
    private function findByCode(Request $request)
    {
        try {
            $code = trim($request->input('code'));

            // Find the order by code
            $order = QROrder::where('code', $code)->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found with the provided code',
                    'errors' => ['code' => ['Order not found with the provided code']]
                ], 404);
            }

            $qrData = json_decode($order->body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Invalid JSON data found in order {$code}: " . $order->body);
                $qrData = ['error' => 'Invalid data format'];
            }

            $itemArray = [];

            if (isset($qrData['items']) && is_array($qrData['items'])) {
                foreach($qrData['items'] as $item) {
                    $product = StoreProduct::find($item['id']);
                    if ($product) {
                        $itemArray[] = [
                            "img" => $product->first_image,
                            "id" => $product->id,
                            "quantity" => $item['q']
                        ];
                    }
                }
            }

            return response()->json([
                "data" => $qrData,
                "items" => $itemArray,
                "code" => $order->code,
                "mode" => "find",
                "order_id" => $order->id,
                "created_at" => $order->created_at->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code find processing error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error finding order: ' . $e->getMessage(),
                'errors' => ['code' => ['Error finding order: ' . $e->getMessage()]]
            ], 500);
        }
    }

    public function order(Request $request) {
        try {
            $randomCode = $this->generateRandomCode(4);

            $order = QROrder::create([
                "body" => $request->data,
                "code" => $randomCode
            ]);

            return response()->json([
                "message" => "success",
            ]);

        } catch (\Exception $e) {
            \Log::error('Order processing error: ' . $e->getMessage());

            return response()->json(['general' => 'Error processing Order: ' . $e->getMessage()]);
        }
    }

    private function generateRandomCode($length = 4) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    private function getNextAvailableCode() {
        $maxCode = QROrder::max('code') ?? 0;
        $nextCode = $maxCode + 1;

        return $nextCode;
    }
}
