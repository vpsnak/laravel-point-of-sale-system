<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'type',
        'payment_id',
        'note'
    ];

    protected $hidden = [
        'refund_type_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
