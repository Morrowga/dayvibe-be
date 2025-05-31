<?php

namespace App\Interfaces\Store;

use App\Models\StoreProduct;
use Illuminate\Http\Request;

interface StoreProductRepositoryInterface
{
    public function index();

    public function indexApi(Request $request);

    public function store(Request $request);

    public function update(Request $request, StoreProduct $storeProduct);

    public function delete(StoreProduct $storeProduct);
}
