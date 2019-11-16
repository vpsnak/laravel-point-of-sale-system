<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasOrder extends Model
{
    protected $fillable = [
        'order_id',
        'mas_id',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
