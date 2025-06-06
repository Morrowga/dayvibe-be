<?php

namespace App\Interfaces\Features;

use App\Models\Trend;
use Illuminate\Http\Request;

interface TrendRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Trend $trend);

    public function delete(Trend $trend);
}
