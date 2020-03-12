<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;

class Transaction extends Model
{
    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $fillable = [
        'payment_type_id',
        'price',
        'change_price',
        'code',
        'status',
        'cash_register_id',
        'order_id',
        'user_id',
    ];

    protected $hidden = [
        'order_id',
        'user_id',
        'payment_type_id',
        'cash_register_id'
    ];

    protected $appends = [
        'transaction_type_name',
        'created_by_name',
        'is_refundable'
    ];

    public function getChangePriceAttribute()
    {
        if (isset($this->attributes['change_price'])) {
            $price = json_decode($this->attributes['change_price'], true);
            return new Money($price['amount'], new Currency($price['currency']));
        } else {
            return new Money(0, $this->price->getCurrency());
        }
    }

    public function setChangePriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $value = json_encode($value);
        }

        $this->attributes['change_price'] = $value;
    }

    public function getPriceAttribute()
    {
        if (isset($this->attributes['price'])) {
            $price = json_decode($this->attributes['price'], true);
            return new Money($price['amount'], new Currency($price['currency']));
        } else {
            return new Money(0, new Currency($this->order()->first()->currency));
        }
    }

    public function setPriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $value = json_encode($value);
        }

        $this->attributes['price'] = $value;
    }

    public function getTransactionTypeNameAttribute()
    {
        return $this->paymentType()->first('name')->name;
    }

    public function getCreatedByNameAttribute()
    {
        return $this->createdBy()->first('name')->name;
    }

    public function getIsRefundableAttribute()
    {
        // @TODO calc if refundable using refund table
        return true;
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function refundType()
    {
        return $this->belongsTo(RefundType::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function elavonApiTransactions()
    {
        return $this->hasMany(ElavonApiTransaction::class);
    }

    public function elavonSdkTransactions()
    {
        return $this->hasMany(ElavonSdkTransaction::class);
    }
}
