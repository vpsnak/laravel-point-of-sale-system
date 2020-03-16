<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;

class Payment extends Model
{
    public $timestamps = false;

    protected $appends = [
        'is_refundable'
    ];

    protected $fillable = [
        'transaction_id',
        'payment_type_id',
        'change_price'
    ];

    protected $hidden = [
        'payment_type_id',
    ];

    public function getChangePriceAttribute()
    {
        if (isset($this->attributes['change_price'])) {
            $price = json_decode($this->attributes['change_price'], true);
            return new Money($price['amount'], new Currency($price['currency']));
        } else {
            return new Money(0, $this->transaction()->without('payment')->first()->price->getCurrency());
        }
    }

    public function setChangePriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $value = json_encode($value);
        }

        $this->attributes['change_price'] = $value;
    }

    public function getTypeNameAttribute()
    {
        return $this->paymentType->name;
    }

    public function getIsRefundableAttribute()
    {
        if ($this->status === 'approved') {
            // @TODO calc if refundable using refund table
            return true;
        } else {
            return false;
        }
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
