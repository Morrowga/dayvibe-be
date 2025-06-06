<?php

namespace App\Interfaces\Features;

use App\Models\Content;
use Illuminate\Http\Request;

interface ContentRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Content $content);

    public function delete(Content $content);
}
