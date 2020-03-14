<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'refund_type_id',
        'payment_id',
        'note'
    ];

    protected $hidden = [
        'refund_type_id',
    ];

    public function getTypeNameAttribute()
    {
        return $this->paymentType()->first('name')->name;
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function refundType()
    {
        return $this->belongsTo(RefundType::class);
    }
}
