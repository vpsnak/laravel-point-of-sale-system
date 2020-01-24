<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonApiPayment extends Model
{
    protected $fillable = [
        'card_number',
        'status',
        'log',
        'txn_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
