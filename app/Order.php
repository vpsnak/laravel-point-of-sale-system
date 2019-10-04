<?php

namespace App;

class Order extends BaseModel
{
    protected $appends = ['total', 'total_paid'];
    
    protected $fillable = [
        'customer_id',
        'store_id',
        'created_by',
        'status',
        'items',
        'discount_type',
        'discount_amount',
        'shipping_type',
        'shipping_cost',
        'tax',
        'subtotal',
        'notes',
    ];
    
    protected $with = ['items', 'payments'];
    
    public function getTotalAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->price * $item->qty;
        };
        return $total;
    }
    
    public function getTotalPaidAttribute()
    {
        $total_paid = 0;
        foreach ($this->payments as $payment) {
            $total_paid += $payment->amount;
        };
        return $total_paid;
    }
    
    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }
}
