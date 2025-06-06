<?php

namespace App\Models;

use App\Traits\UUID;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model implements HasMedia
{
    use HasFactory, UUID, InteractsWithMedia;

    protected $table = "contents";

    protected $fillable = ['title', 'content', 'priority', 'slug', 'brand_id', 'description', 'include_content', 'display_url'];

    protected $appends = ['image_url'];

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_content', 'content_id', 'keyword_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getImageUrlAttribute()
    {
        $media = $this->getFirstMedia('news_image');
        return $media ? $media->getUrl() : null;
    }
}
