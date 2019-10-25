<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonApiPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'cash_register_id',
        'transactionId',
        'paymentGatewayId',
        'chanId',
        'log',
        'payment_id',
        'cash_register_id'
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
