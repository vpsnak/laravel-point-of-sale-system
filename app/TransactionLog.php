<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'payment_id', 'cash_register_id', 'log'
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
