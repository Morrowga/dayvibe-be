<?php

namespace App\Repositories\Store;

use App\Models\Order;
use App\Models\QROrder;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Store\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $orders = QROrder::get();
            return $this->success('Fetched Orders', $orders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request['code'] = $this->generateUniqueCode();

            $newOrder = Order::create($request->all());

            if ($request->hasFile('screenshot')) {
                foreach ($request->file('screenshot') as $image) {
                    $newStoreProduct->addMedia($image)->toMediaCollection('screenshots');
                }
            }


            DB::commit();
            return $this->success('Orrde is created', $newOrder);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    function generateUniqueCode() {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; // Letters for selection
        $digits = '0123456789'; // Digits for selection

        // Generate 5 random letters
        $randomLetters = substr(str_shuffle($letters), 0, 5);

        // Generate 1 random digit
        $randomDigit = $digits[rand(0, strlen($digits) - 1)];

        // Combine and shuffle the letters and digit
        $uniqueCode = str_shuffle($randomLetters . $randomDigit);

        return $uniqueCode;
    }

    public function update(Request $request, QROrder $order)
    {
        DB::beginTransaction();
        try {

            $order->update($request->all());

            DB::commit();
            return $this->success('Order is updated', $order);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(QROrder $order)
    {
        try {
            $order->delete();
            return $this->success('Order is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
