<?php

namespace App\Http\Controllers\Features;

use App\Models\Trend;
use Illuminate\Http\Request;
use App\Http\Requests\TrendRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Features\TrendRepositoryInterface;

class TrendController extends Controller
{
    private TrendRepositoryInterface $trendRepository;

    public function __construct(TrendRepositoryInterface $trendRepository)
    {
        $this->trendRepository = $trendRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trends = $this->trendRepository->index();

        return $trends;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrendRequest $request)
    {
        $newTrend = $this->trendRepository->store($request);

        return $newTrend;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trend $trend)
    {
        $updateTrend = $this->trendRepository->update($request, $trend);

        return $updateTrend;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trend $trend)
    {
        $deleteTrend = $this->trendRepository->delete($trend);

        return $deleteTrend;
    }
}
