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
        'discount',
    ];
}
