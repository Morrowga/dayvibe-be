<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, UUID;

    protected $fillable = [
        'order_id',
        'store_product_id',
        'quantity',
        'price',
        'size_id'
    ];

    protected $table = 'order_items';

    public function storeProduct()
    {
        return $this->belongsTo(StoreProduct::class, 'store_product_id');
    }
}
