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
        'remaining'
        // total_items_cost,
        // total_tax_price,
        // total_price,
    ];

    protected $fillable = [
        'mas_order_id',
        'customer_id',
        'store_id',
        'user_id',
        'items',


        'billing_address',
        'delivery',
        'method',
        'notes',

        'discount',
        'delivery_fees_price',
        'total_items_cost',
        'total_tax_price',
        'total_price',

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
        'discount' => 'array',
        'delivery_fees_price' => 'array',
        'total_tax_price' => 'array',
        'mdse_price' => 'array',
        'delivery_fees_price' => 'array',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function totalTaxPriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['total_tax_price'] = $value;
    }

    public function setMdsePriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['mdse_price'] = $value;
    }

    public function setDeliveryFeesPriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['delivery_fees_price'] = $value;
    }

    public function setTotalTaxPriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['total_tax_price'] = $value;
    }

    public function setDiscountAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['total_tax_price'] = $value;
    }

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
            $value = json_encode($value);
        }
        $this->attributes['delivery']['address'] = $value;
    }

    public function getStorePickupAttribute()
    {
        return $this->delivery['store_pickup'];
    }

    public function setPickupPointAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['delivery']['store_pickup'] = $value;
    }

    public function setDeliveryAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['delivery'] = $value;
    }

    public function setItemsAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['items'] = $value;
    }

    public function getBillingAddressAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setBillingAddressAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['billing_address'] = $value;
    }

    public function getShippingAddressAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setShippingAddressAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['shipping_address'] = $value;
    }

    public function getTotalItemCostAttribute()
    {
        $totalItemCost = $this->total_without_tax - $this->shipping_cost;
        return Price::numberPrecision(abs($totalItemCost));
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

    public function createdBy()
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

    public function lastStatus()
    {
        return $this->statuses()->latest();
    }

    public function getStatusAttribute()
    {
        return $this->lastStatus()->first();
    }

    public static function createWithoutEvents(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return static::create($options);
        });
    }
}
