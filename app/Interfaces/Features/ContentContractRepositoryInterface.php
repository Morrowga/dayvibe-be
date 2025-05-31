<?php

namespace App\Interfaces\Features;

use App\Models\ContentContract;
use Illuminate\Http\Request;

interface ContentContractRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, ContentContract $contract);

    public function delete(ContentContract $contract);
}
