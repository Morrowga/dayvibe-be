<?php

namespace App\Models;

use App\Traits\UUID;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreProduct extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'price', 'stock', 'active', 'category_id', 'has_size'];

    protected $table = 'store_products';

    protected $appends = ['image_urls', 'first_image'];

    protected $casts = [
        'active' => 'boolean',
        'has_size' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('store_product_images')->map(function ($media) {
            return [
                'url' => $media->getUrl(),
            ];
        });
    }

    public function getFirstImageAttribute()
    {
        return $this->getFirstMedia('store_product_images')->getUrl();
    }
}
