<?php

namespace App\Interfaces\Store;

use Illuminate\Http\Request;

interface StoreLocationRepositoryInterface
{
    public function index();

    public function store(Request $request);
}
