<?php

namespace App\Repositories\Features;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\BrandRepositoryInterface;

class BrandRepository implements BrandRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $brands = Brand::with('products', 'categories')->get();
            return $this->success('Fetched Brands', $brands);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newBrand = Brand::create($request->all());
            DB::commit();
            return $this->success('Brand is created', $newBrand);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Brand $brand)
    {
        DB::beginTransaction();
        try {
            $updateBrand = $brand->update($request->all());
            DB::commit();
            return $this->success('Brand is updated', $brand);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Brand $brand)
    {
        try {
            $brand->delete();
            return $this->success('Brand is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
