<?php

namespace App\Repositories\Store;

use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
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

    public function indexApi(Request $request) {
        try {
            $category = $request->query('category');
            $searchQuery = $request->query('q');

            $storeProducts = StoreProduct::with(['category.sizes', 'media'])
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('category', function ($q) use ($category) {
                        $q->where('name_en', $category);
                    });
                })
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->get()
                ->shuffle() // Shuffle the collection
                ->paginate(30); // Note: You'll need to manually paginate

            // Manual pagination since shuffle() returns a Collection
            $currentPage = $request->query('page', 1);
            $perPage = 30;
            $paginatedItems = $storeProducts->forPage($currentPage, $perPage);

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedItems,
                $storeProducts->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return $this->success('Fetched Store Products', $paginator);
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
