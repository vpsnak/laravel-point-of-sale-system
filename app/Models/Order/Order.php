<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $appends = ['total', 'total_without_tax', 'total_paid'];

    protected $fillable = [
        'magento_id',
        'customer_id',
        'store_id',
        'created_by',
        'status',
        'items',
        'discount_type',
        'discount_amount',
        'tax',
        'subtotal',
        'change',
        'shipping_type',
        'shipping_cost',
        'shipping_address_id',
        'billing_address_id',
        'store_pickup_id',
        'delivery_date',
        'delivery_slot',
        'location',
        'occasion',
        'notes',
    ];

    protected $with = [
        'items',
        'payments',
        'customer',
        'store',
        'created_by',
        'shipping_address',
        'store_pickup'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function getTotalAttribute()
    {
        $total = $this->total_without_tax;
        $total = Price::calculateTax($total, $this->tax);

        return round($total, 2);
    }

    public function getTotalWithoutTaxAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->price * $item->qty;
            $total += Price::calculateDiscount($price, $item->discount_type, $item->discount_amount);
        };

        $total = Price::calculateDiscount($total, $this->discount_type, $this->discount_amount);
        $total += $this->shipping_cost;

        return $total;
    }

    public function getTotalPaidAttribute()
    {
        $total_paid = 0;
        foreach ($this->payments as $payment) {
            if ($payment->status === 'approved' && !$payment->refunded) {
                $total_paid += $payment->amount;
            }
        };
        return $total_paid + $this->change;
    }

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function shipping_address()
    {
        return $this->hasOne(OrderAddress::class, 'id', 'shipping_address_id');
    }

    public function billing_address()
    {
        return $this->hasOne(OrderAddress::class, 'id', 'billing_address_id');
    }

    public function store_pickup()
    {
        return $this->hasOne(StorePickup::class, 'id', 'store_pickup_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
