<?php

namespace App;

use App\Helper\Price;

class OrderProduct extends BaseModel
{
    protected $appends = ['final_price'];

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

    public function getFinalPriceAttribute()
    {
        if (!empty($this->discount_type)) {
            return Price::calculateDiscount($this->price, $this->discount_type,
                $this->discount_amount);
        }
        return $this->price;
    }
}
