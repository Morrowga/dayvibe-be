<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory, UUID;

    protected $fillable = [
        'name',
    ];

    protected $table = 'states';

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
