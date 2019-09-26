<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashRegisterLogs extends Model
{
    
    protected $fillable = [
        'user_id',
        'cash_register_id',
        'opening_amount',
        'closing_amount',
        'status',
        'opening_time',
        'closing_time',
        'opened_by',
        'closed_by',
        'note',
    ];
}
