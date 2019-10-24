<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'user_id', 'cash_register_id', 'log'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function payment()
    {
        return $this->belongsTo(User::class);
    }
}
