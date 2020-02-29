<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;
use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;

class Order extends Model
{
    use EagerLoadPivotTrait;

    protected $appends = [
        'status',
        'total',
        'total_without_tax',
        'total_paid',
        'total_tax',
        'total_item_cost',
        'remaining'
    ];

    protected $fillable = [
        'mas_order_id',
        'customer_id',
        'store_id',
        'user_id',
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
        'masOrder',
        'payments',
        'customer',
        'store',
        'created_by'
    ];

    protected $casts = [
        'delivery' => 'array',
        'items' => 'array',
        'shipping_cost' => 'float',
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

    public function getTotalItemCostAttribute()
    {
        $totalItemCost = $this->total_without_tax - $this->shipping_cost;
        return Price::numberPrecision(abs($totalItemCost));
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
            if ($payment->status !== 'failed' && !$payment->status !== 'pending') {
                $total_paid += $payment->amount;
            }
        };
        return Price::numberPrecision($total_paid + $this->change);
    }

    public function getTotalTaxAttribute()
    {
        return Price::numberPrecision($this->total - $this->total_without_tax);
    }

    public function masOrder()
    {
        return $this->hasOne(MasOrder::class);
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function masOrderLog()
    {
        return $this->hasMany(MasOrderLog::class);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class)
            ->using(OrderStatus::class)
            ->withPivot('user_id')
            ->withTimestamps(['created_at']);
    }

    public function processedBy()
    {
        return $this->hasOneThrough(User::class, OrderStatus::class);
    }

    public function getStatusAttribute()
    {
        return  $this->statuses()->latest()->first();
    }
}
