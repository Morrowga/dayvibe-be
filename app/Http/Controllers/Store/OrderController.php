<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\QROrder;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Interfaces\Store\OrderRepositoryInterface;

class OrderController extends Controller
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderRepository->index();

        return Inertia::render('Store/Order/Index', [
            "orders" => $orders['data'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $newOrder = $this->orderRepository->store($request);

        return $newOrder;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QROrder $order)
    {
        return redirect()->route('scanner.index', ['code' => $order->code]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $updateOrder = $this->orderRepository->update($request, $order);

        return redirect()->route('orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $deleteOrder = $this->orderRepository->delete($order);

        return redirect()->route('orders.index');
    }
}
