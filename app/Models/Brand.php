<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\Keyword;
use App\Models\Category;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, UUID;

    protected $table = "brands";

    protected $fillable = ['name', 'description', 'priority', 'slug','founded', 'display_url'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
