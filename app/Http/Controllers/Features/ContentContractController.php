<?php

namespace App\Http\Controllers\Features;

use Illuminate\Http\Request;
use App\Models\ContentContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Interfaces\Features\ContentContractRepositoryInterface;

class ContentContractController extends Controller
{
    private ContentContractRepositoryInterface $contentContractRepository;

    public function __construct(ContentContractRepositoryInterface $contentContractRepository)
    {
        $this->contentContractRepository = $contentContractRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = $this->contentContractRepository->index();

        return $contracts;
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
    public function store(ContractRequest $request)
    {
        $newContract = $this->contentContractRepository->store($request);

        return $newContract;
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
    public function update(Request $request, ContentContract $contract)
    {
        $updateContract = $this->contentContractRepository->update($request, $contract);

        return $updateContract;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentContract $contract)
    {
        $deleteContract = $this->contentContractRepository->delete($contract);

        return $deleteContract;
    }
}
