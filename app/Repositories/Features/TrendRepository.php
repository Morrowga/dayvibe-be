<?php

namespace App\Repositories\Features;

use App\Models\Trend;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\TrendRepositoryInterface;

class TrendRepository implements TrendRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $trends = Trend::get();
            return $this->success('Fetched Trends', $trends);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newTrend = Trend::create($request->all());
            DB::commit();
            return $this->success('Trend is created', $newTrend);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Trend $trend)
    {
        DB::beginTransaction();
        try {
            $updateTrend = $trend->update($request->all());
            DB::commit();
            return $this->success('Trend is updated', $trend);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Trend $trend)
    {
        try {
            $trend->delete();
            return $this->success('Trend is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
