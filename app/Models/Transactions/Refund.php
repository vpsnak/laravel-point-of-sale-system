<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $casts = [
        'price' => 'array',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $with = [
        'refundType',
        'created_by',
        'elavonApiPayments',
        'elavonSdkPayments'
    ];

    protected $fillable = [
        'price',
        'code',
        'status',
        'cash_register_id',
        'order_id',
        'user_id',
    ];

    public function setPriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['price'] = $value;
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
        return $this->belongsTo(User::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    // @TODO convert elavon to polymorphic
    // public function elavonApiPayments()
    // {
    //     return $this->hasMany(ElavonApiPayment::class);
    // }

    // public function elavonSdkPayments()
    // {
    //     return $this->hasMany(ElavonSdkPayment::class);
    // }
}
