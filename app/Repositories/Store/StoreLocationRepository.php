<?php

namespace App\Repositories\Store;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Store\StoreLocationRepositoryInterface;

class StoreLocationRepository implements StoreLocationRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $locations = State::with(['cities'])->get();

            return $this->success('Fetched Locations', $locations);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if($request->type == 'state')
            {
                $model = State::create($request->all());
            } else {
                $state = State::where('name', $request->state)->first();
                if($state)
                {
                    $request['state_id'] = $state->id;

                    $model = City::create($request->all());
                }
            }

            DB::commit();
            return $this->success('Location is created');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

}
