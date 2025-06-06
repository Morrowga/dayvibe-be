<?php

namespace App\Interfaces\Features;

use App\Models\Brand;
use Illuminate\Http\Request;

interface BrandRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Brand $brand);

    public function delete(Brand $brand);
}
