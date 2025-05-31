<?php

namespace App\Repositories\Features;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $products = Product::get();
            return $this->success('Fetched Products', $products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newProduct = Product::create($request->all());
            DB::commit();
            return $this->success('Product is created', $newProduct);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $updateProduct = $product->update($request->all());
            DB::commit();
            return $this->success('Product is updated', $product);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Product $product)
    {
        try {
            $product->delete();
            return $this->success('Product is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
