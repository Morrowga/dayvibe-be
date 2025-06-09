<?php

namespace App\Repositories\Store;

use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
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
            $perPage = 150;
            $page = $request->query('page', 1);
            $seed = $request->query('seed');

            // Always generate seed for new searches (page 1 without existing seed)
            // This makes random ordering the default behavior
            if ($page == 1 && !$seed) {
                $seed = mt_rand(1, 999999);
            }

            $query = StoreProduct::with(['category.sizes', 'media'])
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('category', function ($q) use ($category) {
                        $q->where('name_en', $category);
                    });
                })
                ->when($searchQuery && $searchQuery !== 'New', function ($query) use ($searchQuery) {
                    $query->where('name', 'like', '%' . $searchQuery . '%');
                });

            // Apply ordering - Random is now DEFAULT
            if ($searchQuery === 'New') {
                // Only "New" search uses chronological order
                $query->orderBy('created_at', 'desc');
            } else {
                // DEFAULT: Use random order with seed for all other cases
                $query->orderByRaw("RAND($seed)");
            }

            $products = $query->paginate($perPage);

            // Convert to array and add seed
            $response = $products->toArray();
            $response['seed'] = $seed;

            return $this->success('Fetched Store Products', $response);
        } catch (\Exception $e) {
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
