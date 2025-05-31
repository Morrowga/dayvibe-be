<?php

namespace App\Repositories\Features;

use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use App\Models\ContentContract;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\ContentContractRepositoryInterface;

class ContentContractRepository implements ContentContractRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $contracts = ContentContract::where('active', 1)->get();
            return $this->success('Fetched Contracts', $contracts);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newContract = ContentContract::create($request->all());
            DB::commit();
            return $this->success('Contract is created', $newContract);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, ContentContract $contract)
    {
        DB::beginTransaction();
        try {
            $updateContract = $contract->update($request->all());
            DB::commit();
            return $this->success('Contract is updated', $contract);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(ContentContract $contract)
    {
        try {
            $contract->delete();
            return $this->success('Contract is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
