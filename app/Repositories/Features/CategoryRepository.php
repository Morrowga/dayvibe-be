<?php

namespace App\Repositories\Features;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $categories = Category::with(['sizes'])->get();
            return $this->success('Fetched Categories', $categories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newCategory = Category::create($request->all());

            if ($request->has('sizes') && is_array($request->sizes)) {
                $newCategory->sizes()->sync($request->sizes);
            }

            DB::commit();
            return $this->success('Category is created', $newCategory);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Category $category)
    {
        DB::beginTransaction();
        try {
            $updateCategory = $category->update($request->all());

            if ($request->has('sizes') && is_array($request->sizes)) {
                $category->sizes()->sync($request->sizes);
            }

            DB::commit();
            return $this->success('Category is updated', $category);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Category $category)
    {
        try {
            $category->delete();
            return $this->success('Category is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
