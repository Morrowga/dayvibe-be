<?php

namespace App\Interfaces\Features;

use App\Models\Keyword;
use Illuminate\Http\Request;

interface KeywordRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Keyword $keyword);

    public function delete(Keyword $keyword);
}
