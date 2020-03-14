<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;
use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Helper\PhpHelper;

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
        'delivery_fees_price',
        'discount',
        // auto fill-handle
        'store_id',
        'currency',
        'tax_percentage',
        'created_by_id',
        'mas_order_id',
        'magento_id',
        'magento_shipping_address_id',
        'magento_billing_address_id'
    ];

    protected $with = [
        'masOrder',
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

    public function setDeliveryAddressAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['delivery']['address'] = $value;
    }

    public function getDeliveryAttribute()
    {
        return json_decode($this->attributes['delivery'], true);
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

        if (isset($this->discount) && isset($this->discount['type']) && isset($this->discount['amount'])) {
            switch ($this->discount['type']) {
                case 'flat':
                    $amount = new Money($this->discount['amount'], new Currency($this->currency));
                    $mdsePrice = $mdsePrice->subtract($amount);
                    break;
                case 'percentage':
                    $amount = $this->discount['amount'];
                    $discount = $mdsePrice->multiply($amount)->divide(100);
                    $mdsePrice = $mdsePrice->subtract($discount);
                    break;
                default:
                    break;
            }
        }
        return $mdsePrice;
    }

    public function getDeliveryFeesPriceAttribute()
    {
        if (isset($this->attributes['delivery_fees_price'])) {
            $price = json_decode($this->attributes['delivery_fees_price'], true);
            $currency = $price['currency'] ?? $this->currency;
            return new Money($price['amount'], new Currency($currency));
        } else {
            return new Money(0,  new Currency($this->currency));
        }
    }

    public function setDeliveryFeesPriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $value = json_encode($value);
        }

        $this->attributes['delivery_fees_price'] = $value;
    }

    public function getTaxPriceAttribute()
    {
        return $this
            ->mdse_price
            ->add($this->delivery_fees_price)
            ->multiply($this->tax_percentage / 100);
    }

    public function getTotalPriceAttribute()
    {
        return $this
            ->tax_price
            ->add($this->mdse_price)
            ->add($this->delivery_fees_price);
    }

    public function getPaidPriceAttribute()
    {
        $paidPrice = new Money(0, new Currency($this->currency));
        foreach ($this->transactions as $transaction) {
            if (!empty($transaction->payment) && $transaction->status === 'approved') {
                $paidPrice = $paidPrice->add($transaction->price);
                $paidPrice = $paidPrice->subtract($transaction->payment->change_price);
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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
        return $this->belongsTo(User::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function masOrderLog()
    {
        return $this->hasMany(MasOrderLog::class);
    }

    public function cards()
    {
        return $this->morphMany(Card::class, 'cardable');
    }

    public function statuses()
    {
        return $this
            ->belongsToMany(Status::class)
            ->using(OrderStatus::class)
            ->withPivot('processed_by_id')
            ->withTimestamps(['created_at'])
            ->orderBy('created_at', 'desc');
    }

    public function lastStatus()
    {
        return $this->statuses()->latest();
    }

    public function getStatusAttribute()
    {
        return $this->lastStatus()->first();
    }
}
