<?php

namespace App;

use App\Helper\Price;

class Order extends BaseModel
{
    protected $appends = ['total', 'total_without_tax', 'total_paid'];

    protected $fillable = [
        'customer_id',
        'store_id',
        'created_by',
        'status',
        'items',
        'discount_type',
        'discount_amount',
        'tax',
        'subtotal',
        'shipping_type',
        'shipping_cost',
        'shipping_address',
        'delivery_date',
        'notes',
    ];

    protected $with = ['items', 'payments', 'customer', 'store_id', 'created_by'];

    public function getTotalAttribute()
    {
        $total = $this->total_without_tax;
        $total += $this->shipping_cost;
        $total = Price::calculateTax($total, $this->tax);

        return $total;
    }

    public function getTotalWithoutTaxAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->price * $item->qty;
            $total += Price::calculateDiscount($price, $item->discount_type, $item->discount_amount);
        };

        $total = Price::calculateDiscount($total, $this->discount_type, $this->discount_amount);

        return $total;
    }

    public function getTotalPaidAttribute()
    {
        $total_paid = 0;
        foreach ($this->payments as $payment) {
            if ($payment->status === 'approved') {
                $total_paid += $payment->amount;
            }
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store_id()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
