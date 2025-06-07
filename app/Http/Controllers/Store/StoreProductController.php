<?php

namespace App\Http\Controllers\Store;

use App\Models\Size;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreProductUpdateRequest;
use App\Interfaces\Store\StoreProductRepositoryInterface;

class StoreProductController extends Controller
{
    private StoreProductRepositoryInterface $storeProductRepository;

    public function __construct(StoreProductRepositoryInterface $storeProductRepository)
    {
        $this->storeProductRepository = $storeProductRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storeProducts = $this->storeProductRepository->index();

        return Inertia::render('Store/StoreProduct/Index', [
            "storeProducts" => $storeProducts['data'],
        ]);
    }

    public function indexApi(Request $request)
    {
       $storeProducts = $this->storeProductRepository->indexApi($request);

       return $storeProducts;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        $sizes = Size::get();

        return Inertia::render('Store/StoreProduct/CreateEdit', [
            "categories" => $categories,
            "sizes" => $sizes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $newStoreProduct = $this->storeProductRepository->store($request);

        return redirect()->route('store-products.index');
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
    public function edit(StoreProduct $store_product)
    {
        $categories = Category::get();

        return Inertia::render('Store/StoreProduct/CreateEdit',[
            "store_product" => $store_product,
            "categories" => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductUpdateRequest $request, StoreProduct $store_product)
    {
        $updateStoreProduct = $this->storeProductRepository->update($request, $store_product);

        return redirect()->route('store-products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreProduct $store_product)
    {
        $deleteStoreProduct = $this->storeProductRepository->delete($store_product);

        return redirect()->route('store-products.index');
    }
}
