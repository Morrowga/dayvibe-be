<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = "categories";

    protected $fillable = ['name_mm', 'name_en', 'is_include_detail'];

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'category_sizes', 'category_id', 'size_id');
    }
}
