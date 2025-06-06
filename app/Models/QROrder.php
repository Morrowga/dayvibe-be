<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QROrder extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'body'];

    protected $table = 'q_r_orders';

    public function getDecodedBodyAttribute()
    {
        return json_decode($this->body, true);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = is_array($value) ? json_encode($value) : $value;
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
