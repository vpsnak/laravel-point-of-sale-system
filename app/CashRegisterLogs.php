<?php

namespace App;

class CashRegisterLogs extends BaseModel
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
