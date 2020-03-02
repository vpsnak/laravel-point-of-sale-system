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

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
