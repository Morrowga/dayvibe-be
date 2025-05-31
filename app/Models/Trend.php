<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ContentContract;

class Trend extends Model
{
    use HasFactory, UUID;

    protected $table = "trends";

    protected $fillable = ['content_id', 'content_contract_id','priority'];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function contract()
    {
        return $this->belongsTo(ContentContract::class);
    }
}
