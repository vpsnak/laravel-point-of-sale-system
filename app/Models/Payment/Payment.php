<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'refunded' => 'boolean',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    protected $with = [
        'paymentType',
        'created_by',
        'elavonApiPayments',
        'elavonSdkPayments'
    ];

    protected $fillable = [
        'payment_type',
        'amount',
        'code',
        'status',
        'refunded',
        'cash_register_id',
        'order_id',
        'created_by',
    ];

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function elavonApiPayments()
    {
        return $this->hasMany(ElavonApiPayment::class);
    }

    public function elavonSdkPayments()
    {
        return $this->hasMany(ElavonSdkPayment::class);
    }
}
