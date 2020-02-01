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

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
