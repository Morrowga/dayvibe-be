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
        return "success";
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

            foreach($qrData['items'] as $item)
            {
                $product = StoreProduct::find($item['id']);
                $itemArray[] = [
                    "img" => $product->first_image,
                    "quantity" => $item['q']
                ];
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
            $order = QROrder::create([
                "body" => $request->data,
                "code" => $request->code
            ]);

            return response()->json([
                "message" => "success",
            ]);

        } catch (\Exception $e) {
            \Log::error('Order processing error: ' . $e->getMessage());

            return response()->json(['general' => 'Error processing Order: ' . $e->getMessage()]);
        }
    }

    private function getNextAvailableCode() {
        $maxCode = QROrder::max('code') ?? 0;
        $nextCode = $maxCode + 1;

        return $nextCode;
    }
}
