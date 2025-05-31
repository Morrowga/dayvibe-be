<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySize extends Model
{
    use HasFactory;

    protected $table = 'category_sizes';

    protected $fillable = ['category_id', 'size_id'];
}
