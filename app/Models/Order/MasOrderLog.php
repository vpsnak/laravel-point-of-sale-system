<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasOrderLog extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'payload',
        'response',
        'environment'
    ];

    public function masOrder()
    {
        $this->belongsTo(Order::class);
    }
}
