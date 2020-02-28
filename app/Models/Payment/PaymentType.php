<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $timestamps = false;

    protected $casts = [
        'status' => 'boolean',
        'hidden' => 'boolean'
    ];

    public static function getPaymentTypes()
    {
        return self::whereStatus(true)->whereHidden(false)->get();
    }

    public static function getRefundTypes()
    {
        return self::whereStatus(false)->whereHidden(false)->get();
    }
}
