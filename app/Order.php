<?php

namespace App;

class Order extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'items',
        'discount_type',
        'discount',
        'shipping_type',
        'shipping_cost',
        'tax',
        'subtotal',
        'note',
    ];

    protected $with = ['items', 'payments'];

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class);
    }
}
