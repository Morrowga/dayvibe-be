<?php

namespace App\Interfaces\Features;

use App\Models\Product;
use Illuminate\Http\Request;

interface SearchRepositoryInterface
{
    public function searchBrands();

    public function searchContents();

    public function contentDetail($displayUrl);
}
