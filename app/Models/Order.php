<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\OrderItem;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model implements HasMedia
{
    use HasFactory, UUID, InteractsWithMedia;

    protected $fillable = [
        'name',
        'code',
        'msisdn',
        'address',
        'state',
        'city',
        'status',
        'total_price',
        'total_quantity',
        'content'
    ];

    protected $table = 'orders';

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
