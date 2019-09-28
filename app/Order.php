<?php

namespace App;

class Order extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'store_id',
        'created_by',
        'items',
        'discount_type',
        'discount_amount',
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
