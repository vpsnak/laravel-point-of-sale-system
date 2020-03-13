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
        'opened_by_id',
        'closed_by_id',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class);
    }
}
