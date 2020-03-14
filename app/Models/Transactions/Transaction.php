<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;

class Transaction extends Model
{
    protected $appends = [
        'type',
        'type_name',
        'created_by_name'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $fillable = [
        'price',
        'code',
        'status',
        'cash_register_id',
        'order_id',
        'created_by_id',
    ];

    protected $hidden = [
        'order_id',
        'created_by_id',
        'cash_register_id'
    ];

    protected $with = [
        'payment',
        'refund'
    ];

    public function getTypeAttribute()
    {
        if ($this->payment) {
            return 'payment';
        } else {
            return 'refund';
        }
    }

    public function getTypeNameAttribute()
    {
        return $this->payment->type_name ?? $this->payment->type_name;
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

    public function getCreatedByNameAttribute()
    {
        return $this->createdBy()->first('name')->name;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
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

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
