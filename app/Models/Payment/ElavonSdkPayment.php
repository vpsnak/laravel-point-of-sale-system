<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonSdkPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'transactionId',
        'paymentGatewayId',
        'chanId',
        'log',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
