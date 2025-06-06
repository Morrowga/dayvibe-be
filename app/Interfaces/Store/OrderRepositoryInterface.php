<?php

namespace App\Interfaces\Store;

use App\Models\Order;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Order $order);

    public function delete(Order $order);
}
