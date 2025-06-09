<?php

namespace App\Repositories\Store;

use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Store\StoreProductRepositoryInterface;

class StoreProductRepository implements StoreProductRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $storeProducts = StoreProduct::with(['category', 'media'])->orderBy('id', 'DESC')->get();
            return $this->success('Fetched Store Products', $storeProducts);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function indexApi(Request $request)
    {
        try {
            $category = $request->query('category');
            $searchQuery = $request->query('q');
            $perPage = 30;
            $page = $request->query('page', 1);

            Log::info('Product API Request', [
                'category' => $category,
                'search' => $searchQuery,
                'page' => $page
            ]);

            // Create a unique cache/session key for this search context
            $searchContext = md5(
                ($category ?? '') . '_' .
                ($searchQuery ?? '') . '_' .
                session()->getId()
            );

            $sessionKey = 'product_order_' . $searchContext;

            // For "New" searches, we don't use random ordering
            if ($searchQuery === 'New') {
                return $this->handleNewProducts($request, $category, $perPage, $page);
            }

            // Get or create a shuffled list of product IDs for this search context
            if (!session()->has($sessionKey) || $page === 1) {
                $this->generateShuffledProductIds($sessionKey, $category, $searchQuery);
            }

            $shuffledIds = session()->get($sessionKey, []);

            if (empty($shuffledIds)) {
                return $this->success('Fetched Store Products', $this->createEmptyPaginationResponse());
            }

            // Calculate pagination
            $totalProducts = count($shuffledIds);
            $totalPages = ceil($totalProducts / $perPage);
            $offset = ($page - 1) * $perPage;

            // Get the IDs for current page
            $currentPageIds = array_slice($shuffledIds, $offset, $perPage);

            if (empty($currentPageIds)) {
                return $this->success('Fetched Store Products', $this->createEmptyPaginationResponse());
            }

            // Fetch products in the exact order of shuffled IDs
            $products = $this->getProductsInOrder($currentPageIds);

            // Create pagination response that matches your frontend expectations
            $paginationData = [
                'data' => $products,
                'current_page' => (int) $page,
                'last_page' => $totalPages,
                'per_page' => $perPage,
                'total' => $totalProducts,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalProducts),
                'has_more_pages' => $page < $totalPages,
                'next_page_url' => $page < $totalPages ? url()->current() . '?' . http_build_query(array_merge($request->query(), ['page' => $page + 1])) : null,
                'prev_page_url' => $page > 1 ? url()->current() . '?' . http_build_query(array_merge($request->query(), ['page' => $page - 1])) : null,
            ];

            Log::info('Product API Response', [
                'total_products' => $totalProducts,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'returned_products' => count($products)
            ]);

            return $this->success('Fetched Store Products', $paginationData);

        } catch (\Exception $e) {
            Log::error('Product indexApi error: ' . $e->getMessage(), [
                'category' => $category ?? 'null',
                'searchQuery' => $searchQuery ?? 'null',
                'page' => $page ?? 'null',
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Handle "New" products with date ordering (no randomization)
     */
    private function handleNewProducts($request, $category, $perPage, $page)
    {
        $query = StoreProduct::with(['category.sizes', 'media'])
            ->when($category, function ($query) use ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('name_en', $category);
                });
            })
            ->orderBy('created_at', 'desc');

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform to match expected format
        $responseData = [
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
            'has_more_pages' => $products->hasMorePages(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ];

        return $this->success('Fetched Store Products', $responseData);
    }

    /**
     * Generate and store shuffled product IDs for consistent pagination
     */
    private function generateShuffledProductIds($sessionKey, $category, $searchQuery)
    {
        $query = StoreProduct::query()
            ->when($category, function ($query) use ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('name_en', $category);
                });
            })
            ->when($searchQuery && $searchQuery !== '', function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%');
            });

        // Get all matching product IDs
        $productIds = $query->pluck('id')->toArray();

        // Shuffle the IDs randomly
        shuffle($productIds);

        // Store in session for consistent pagination
        session()->put($sessionKey, $productIds);

        Log::info('Generated shuffled product IDs', [
            'session_key' => $sessionKey,
            'total_products' => count($productIds)
        ]);

        return $productIds;
    }

    /**
     * Get products in the exact order specified by IDs array
     */
    private function getProductsInOrder($productIds)
    {
        if (empty($productIds)) {
            return [];
        }

        // Create a case statement to maintain the exact order
        $orderCase = 'CASE id ';
        foreach ($productIds as $index => $id) {
            $orderCase .= "WHEN {$id} THEN {$index} ";
        }
        $orderCase .= 'END';

        $products = StoreProduct::with(['category.sizes', 'media'])
            ->whereIn('id', $productIds)
            ->orderByRaw($orderCase)
            ->get();

        return $products;
    }

    /**
     * Create empty pagination response
     */
    private function createEmptyPaginationResponse()
    {
        return [
            'data' => [],
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 30,
            'total' => 0,
            'from' => null,
            'to' => null,
            'has_more_pages' => false,
            'next_page_url' => null,
            'prev_page_url' => null,
        ];
    }

    /**
     * Reset the random order for a fresh shuffle
     * This endpoint can be called when user wants to refresh the random order
     */
    public function resetRandomOrder(Request $request)
    {
        try {
            $category = $request->query('category');
            $searchQuery = $request->query('q');

            $searchContext = md5(
                ($category ?? '') . '_' .
                ($searchQuery ?? '') . '_' .
                session()->getId()
            );

            $sessionKey = 'product_order_' . $searchContext;

            // Remove the existing shuffled order
            session()->forget($sessionKey);

            Log::info('Random order reset', [
                'session_key' => $sessionKey,
                'category' => $category,
                'search_query' => $searchQuery
            ]);

            return $this->success('Random order reset successfully');

        } catch (\Exception $e) {
            Log::error('Reset random order error: ' . $e->getMessage());
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Clear all product ordering sessions (useful for testing or admin)
     */
    public function clearAllProductSessions(Request $request)
    {
        try {
            // Get all session data
            $sessionData = session()->all();

            // Find and remove all product order keys
            $removedKeys = 0;
            foreach ($sessionData as $key => $value) {
                if (strpos($key, 'product_order_') === 0) {
                    session()->forget($key);
                    $removedKeys++;
                }
            }

            Log::info('Cleared product order sessions', [
                'removed_keys' => $removedKeys
            ]);

            return $this->success("Cleared {$removedKeys} product order sessions");

        } catch (\Exception $e) {
            Log::error('Clear sessions error: ' . $e->getMessage());
            return $this->error($e->getMessage(), 500);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $newStoreProduct = StoreProduct::create($request->all());

            $newStoreProduct->load(['category', 'media']);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $newStoreProduct->addMedia($image)->toMediaCollection('store_product_images');
                }
            }

            DB::commit();
            return $this->success('Store Product is created', $newStoreProduct);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, StoreProduct $storeProduct)
    {
        DB::beginTransaction();
        try {

            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $mediaId) {
                    $media = $storeProduct->getMedia('store_product_images')->find($mediaId);
                    if ($media) {
                        $media->delete();
                    }
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $storeProduct->addMedia($image)->toMediaCollection('store_product_images');
                }
            }

           $updateStoreProduct = $storeProduct->update($request->all());

            DB::commit();
            return $this->success('Store Product is updated', $storeProduct);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(StoreProduct $storeProduct)
    {
        try {
            $storeProduct->delete();
            return $this->success('Store Product is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
