<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'print_count',
        'email_count',
        'content',
        'issued_by',
        'order_id',
        'cash_register_id'
    ];

    protected $casts = [
        'content' => 'array',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function cash_registers()
    {
        return $this->belongsTo(CashRegister::class);
    }
}