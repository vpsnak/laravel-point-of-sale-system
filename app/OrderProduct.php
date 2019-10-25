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
        'notes'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }
}
