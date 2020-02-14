<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $appends = [
        'total',
        'total_without_tax',
        'total_paid',
        'total_tax',
        'remaining'
    ];

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
        'change',
        'shipping_cost',
        'billing_address',
        'delivery',
        'method',
        'notes',

        'magento_id',
        'magento_shipping_address_id',
        'magento_billing_address_id',
    ];

    protected $with = [
        'payments',
        'customer',
        'store',
        'created_by'
    ];

    protected $casts = [
        'delivery' => 'array',
        'items' => 'array',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function getRemainingAttribute()
    {
        $remaining = Price::numberPrecision($this->total - $this->total_paid);

        if ($remaining < 0) {
            return 0;
        } else {
            return $remaining;
        }
    }

    public function getDeliveryAddressAttribute()
    {
        return $this->delivery['address'];
    }

    public function setDeliveryAddressAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['delivery']['address'] = json_encode($value);
        } else {
            $this->attributes['delivery']['address'] = $value;
        }
    }

    public function getStorePickupAttribute()
    {
        return $this->delivery['store_pickup'];
    }

    public function setPickupPointAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['delivery']['store_pickup'] = json_encode($value);
        } else {
            $this->attributes['delivery']['store_pickup'] = $value;
        }
    }

    public function setDeliveryAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['delivery'] = json_encode($value);
        } else {
            $this->attributes['delivery'] = $value;
        }
    }

    // public function getItemsAttribute($value)
    // {
    //     return json_decode($value, true);
    // }

    public function setItemsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['items'] = json_encode($value);
        } else {
            $this->attributes['items'] = $value;
        }
    }

    public function getBillingAddressAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setBillingAddressAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['billing_address'] = json_encode($value);
        } else {
            $this->attributes['billing_address'] = $value;
        }
    }

    public function getShippingAddressAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setShippingAddressAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['shipping_address'] = json_encode($value);
        } else {
            $this->attributes['shipping_address'] = $value;
        }
    }

    public function getTotalAttribute()
    {
        $total = $this->total_without_tax;
        $total = Price::calculateTax($total, $this->tax);

        return Price::numberPrecision(($total * 100) / 100);
    }

    public function getTotalWithoutTaxAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item['price'] * (int) $item['qty'];
            $total += Price::calculateDiscount($price, $item['discount_type'], $item['discount_amount']);
        };

        $total = Price::calculateDiscount($total, $this->discount_type, $this->discount_amount);
        $total += $this->shipping_cost;

        return Price::numberPrecision($total);
    }

    public function getTotalPaidAttribute()
    {
        $total_paid = 0;
        foreach ($this->payments as $payment) {
            if ($payment->status === 'approved' && !$payment->refunded) {
                $total_paid += $payment->amount;
            }
        };
        return Price::numberPrecision($total_paid + $this->change);
    }

    public function getTotalTaxAttribute()
    {
        return Price::numberPrecision($this->total - $this->total_without_tax);
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

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
