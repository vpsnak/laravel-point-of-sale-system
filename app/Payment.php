<?php

namespace App;

class Payment extends BaseModel
{
    protected $with = ['paymentType', 'order', 'created_by'];

    protected $fillable = [
        'payment_type',
        'amount',
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
}
