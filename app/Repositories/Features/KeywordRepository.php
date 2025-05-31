<?php

namespace App\Repositories\Features;

use App\Models\Keyword;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\KeywordRepositoryInterface;

class KeywordRepository implements KeywordRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $keywords = Keyword::get();
            return $this->success('Fetched Keywords', $keywords);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newKeyword = Keyword::firstOrCreate(["name" => $request->name], $request->all());
            DB::commit();
            return $this->success('Keyword is created', $newKeyword);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Keyword $keyword)
    {
        DB::beginTransaction();
        try {
            $updateKeyword = $keyword->update($request->all());
            DB::commit();
            return $this->success('Keyword is updated', $keyword);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Keyword $keyword)
    {
        try {
            $keyword->delete();
            return $this->success('Keyword is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
