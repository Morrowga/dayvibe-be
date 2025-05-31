<?php

namespace App\Http\Controllers\Store;

use App\Models\Size;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\SizeRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Store\SizeRepositoryInterface;

class SizeController extends Controller
{
    private SizeRepositoryInterface $sizeRepository;

    public function __construct(SizeRepositoryInterface $sizeRepository)
    {
        $this->sizeRepository = $sizeRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = $this->sizeRepository->index();

        return Inertia::render('Sizes/Index', [
            "sizes" => $sizes['data'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Sizes/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        $newSize = $this->sizeRepository->store($request);

        return redirect()->route('sizes.index');
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
    public function edit(Size $size)
    {
        return Inertia::render('Sizes/CreateEdit', [
            "size" => $size
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SizeRequest $request, Size $size)
    {
        $updateSize = $this->sizeRepository->update($request, $size);

        return redirect()->route('sizes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $deleteSize = $this->sizeRepository->delete($size);

        return redirect()->route('sizes.index');
    }
}
