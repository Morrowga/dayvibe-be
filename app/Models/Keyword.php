<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keyword extends Model
{
    use HasFactory, UUID;

    protected $table = "keywords";

    protected $fillable = ['name'];

    public function contents()
    {
        return $this->belongsToMany(Content::class);
    }
}
