<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class Payment extends Model
{
    public $timestamps = false;

    protected $appends = [
        'earnings_price',
        'refundable_price'
    ];

    protected $fillable = [
        'transaction_id',
        'payment_type_id',
        'change_price'
    ];

    protected $hidden = [
        'payment_type_id',
    ];

    public function getEarningsPriceAttribute()
    {
        $transaction = $this->transaction()->without('payment')->first();
        return $transaction->price->subtract($this->change_price);
    }

    public function getChangePriceAttribute()
    {
        if (isset($this->attributes['change_price'])) {
            $price = json_decode($this->attributes['change_price'], true);
            return Price::parsePrice($price);
        } else {
            return Price::parsePrice([
                'amount' => 0,
                'currency' => $this->transaction()->without('payment')->first()->price->getCurrency()
            ]);
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

    public function getRefundablePriceAttribute()
    {
        $transaction = $this->transaction()->without('payment')->first();
        $refundablePrice = $transaction->price->subtract($this->change_price);
        if ($transaction->status === 'approved' && $transaction->payment->paymentType->type !== 'coupon') {
            foreach ($this->refunds as $refund) {
                $refundTransaction = $refund->transaction;
                if ($refundTransaction->status === 'refund approved') {
                    $refundablePrice = $refundablePrice->subtract($refundTransaction->price);
                }
            }
            return $refundablePrice;
        }
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
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
