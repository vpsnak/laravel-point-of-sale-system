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
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function setLogAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['log'] = json_encode($value);
        } else {
            $this->attributes['log'] = $value;
        }
    }

    public function getLogAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getPaymentTransactionData()
    {
        return $this->log['data']['paymentGatewayCommand']['paymentTransactionData'];
    }
}
