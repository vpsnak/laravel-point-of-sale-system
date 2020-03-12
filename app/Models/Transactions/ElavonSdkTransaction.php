<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonSdkTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'paymentGatewayId',
        'chanId',
        'log',
    ];

    protected $casts = [
        'log' => 'array',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function setLogAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['log'] = json_encode($value);
        } else {
            $this->attributes['log'] = $value;
        }
    }

    public function getTransactionDataAttribute()
    {
        return $this->log['data']['paymentGatewayCommand']['paymentTransactionData'];
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
