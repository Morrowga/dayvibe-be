<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentContract extends Model
{
    use HasFactory, UUID;

    protected $table = "content_contracts";

    protected $fillable = ['start_date', 'end_date', 'brand_id', 'priority', 'amount', 'active'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
