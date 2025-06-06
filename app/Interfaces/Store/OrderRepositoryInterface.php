<?php

namespace App\Interfaces\Store;

use App\Models\QROrder;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, QROrder $order);

    public function delete(QROrder $order);
}
