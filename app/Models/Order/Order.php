<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;
use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;


class Order extends Model
{
    use EagerLoadPivotTrait;

    protected $appends = [
        'status',
        'mdse_price',
        'tax_price',
        'total_price',
        'paid_price',
        'remaining_price',
    ];

    protected $fillable = [
        // validation
        'method',
        'items',
        'notes',
        'customer_id',
        'billing_address',
        'delivery',
        'delivery_fees_amount',
        'discount',
        // auto fill-handle
        'store_id',
        'currency',
        'tax_percentage',
        'user_id',
        'mas_order_id',
        'magento_id',
        'magento_shipping_address_id',
        'magento_billing_address_id'
    ];

    protected $with = [
        'masOrder',
        'payments',
        'customer',
        'store',
        'createdBy'
    ];

    protected $hidden = [
        'customer_id',
        'store_id',
        'user_id',
        'mas_order_id',
        'magento_id',
        'magento_shipping_address_id',
        'magento_billing_address_id'
    ];

    protected $casts = [
        'discount' => 'array',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function setDiscountAttribute($value)
    {
        if (is_array($value)) {
            if (!isset($value['amount']) || !$value['amount']) {
                $value['type'] = 'none';
                $value['amount'] = null;
            }
            $value = json_encode($value);
        }
        $this->attributes['discount'] = $value;
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

    public function getItemsAttribute($value)
    {
        return json_decode($value, true);
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

    public function getMdsePriceAttribute()
    {
        $mdsePrice = new Money(0, new Currency($this->currency));
        foreach ($this->items as $item) {
            $price = new Money($item['price']['amount'], new Currency($this->currency));
            $price = $price->multiply($item['qty']);
            if (isset($item['discount']) && isset($item['discount']['type']) && isset($item['discount']['amount'])) {
                switch ($item['discount']['type']) {
                    case 'flat':
                        $price = $price->subtract(new Money($item['discount']['amount'], new Currency($this->currency)));
                        break;
                    case 'percentage':
                        $price = $price->multiply($item['discount']['amount'])->divide(100);
                        break;
                    default:
                    case 'none':
                        break;
                }
            }
            $mdsePrice = $mdsePrice->add($price);
        }
        return $mdsePrice;
    }

    public function getTaxPriceAttribute()
    {
        return $this
            ->mdse_price
            ->add(new Money($this->delivery_fees_amount, new Currency($this->currency)))
            ->multiply($this->tax_percentage / 100);
    }

    public function getTotalPriceAttribute()
    {
        return $this
            ->tax_price
            ->add($this->mdse_price)
            ->add(new Money($this->delivery_fees_amount, new Currency($this->currency)));
    }

    public function getPaidPriceAttribute()
    {
        $paidPrice = new Money($this->delivery_fees_amount, new Currency($this->currency));
        foreach ($this->payments as $payment) {
            if ($payment->status === 'approved') {
                $paidPrice = $paidPrice->add($payment->price);
                $paidPrice = $paidPrice->subtract($payment->change_price);
            }
        }
        return $paidPrice;
    }

    public function getRemainingPriceAttribute()
    {
        return $this->total_price->subtract($this->paid_price);
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
        return $this
            ->belongsToMany(Status::class)
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
