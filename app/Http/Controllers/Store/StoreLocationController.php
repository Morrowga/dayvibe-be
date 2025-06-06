<?php

namespace App\Http\Controllers\Store;

use App\Models\State;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Store\StoreLocationRepositoryInterface;

class StoreLocationController extends Controller
{
    private StoreLocationRepositoryInterface $storeLocationRepository;

    public function __construct(StoreLocationRepositoryInterface $storeLocationRepository)
    {
        $this->storeLocationRepository = $storeLocationRepository;
    }

    public function index()
    {
        $storeLocations = $this->storeLocationRepository->index();

        return $storeLocations;
    }


    public function store(Request $request)
    {
        $storeLocations = $this->storeLocationRepository->store($request);

        return $storeLocations;
    }
}
