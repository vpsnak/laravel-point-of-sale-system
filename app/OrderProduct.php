<?php

namespace App;

class OrderProduct extends BaseModel
{
    protected $fillable = [
        'order_id',
        'name',
        'sku',
        'price',
        'qty',
        'discount_type',
        'discount_amount',
        'note'
    ];

    public function order()
    {
        return $this->belongsTo(\App\Order::class);
    }
}
