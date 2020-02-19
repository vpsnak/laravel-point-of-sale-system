<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $timestamps = false;

    protected $casts = [
        'status' => 'boolean'
    ];

    public static function getPaymentTypes()
    {
        return self::whereStatus(true)->get();
    }

    public static function getRefundTypes()
    {
        return self::whereStatus(false)->get();
    }
}
