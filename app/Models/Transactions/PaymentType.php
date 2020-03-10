<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $timestamps = false;

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function enabled()
    {
        return static::where('is_enabled', true)->get();
    }
}
