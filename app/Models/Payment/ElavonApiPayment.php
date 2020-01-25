<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonApiPayment extends Model
{
    protected $fillable = [
        'txn_id',
        'card_number',
        'card_holder',
        'status',
        'transaction',
        'log',
        'payment_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
