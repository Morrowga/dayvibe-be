<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ScannerRepositoryInterface
{
    public function index();
    
    public function store(Request $request);

    public function order(Request $request);
}
