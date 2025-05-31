<?php

namespace App\Repositories\Store;

use App\Models\Size;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Store\SizeRepositoryInterface;

class SizeRepository implements SizeRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $sizes = Size::get();
            return $this->success('Fetched Sizes', $sizes);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newSize = Size::create($request->all());
            DB::commit();
            return $this->success('Size is created', $newSize);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Size $size)
    {
        DB::beginTransaction();
        try {
            $updateSize = $size->update($request->all());
            DB::commit();
            return $this->success('Size is updated', $size);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Size $size)
    {
        try {
            $size->delete();
            return $this->success('Size is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
