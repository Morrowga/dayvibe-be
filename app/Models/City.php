<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory, UUID;

    protected $fillable = [
        'name',
        'price',
        'state_id'
    ];

    protected $table = 'cities';
}
