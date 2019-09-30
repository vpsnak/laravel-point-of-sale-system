<?php

namespace App;

class Payment extends BaseModel
{
    protected $with = ['paymentType','order'];
    
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
}
