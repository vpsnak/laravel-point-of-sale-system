<?php

namespace App;

class Payment extends BaseModel
{
    protected $with = ['paymentType', 'created_by'];

    protected $fillable = [
        'payment_type',
        'amount',
        'code',
        'cash_register_id',
        'order_id',
        'created_by',
    ];

    public function transactionLog()
    {
        return $this->hasMany(TransactionLog::class);
    }

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
}
