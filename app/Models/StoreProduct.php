<?php

namespace App\Models;

use App\Traits\UUID;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
            // Use webp if it exists, otherwise fallback to original
            $webpUrl = $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : $media->getUrl();

            return [
                'url' => $webpUrl,
            ];
        });
    }

    public function getFirstImageAttribute()
    {
        $media = $this->getFirstMedia('store_product_images');

        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('webp')
            ? $media->getUrl('webp')
            : $media->getUrl();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(75)
            ->nonQueued(); // remove this line if using queue
    }
}
