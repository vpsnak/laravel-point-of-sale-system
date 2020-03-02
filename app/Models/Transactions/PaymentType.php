<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $timestamps = false;

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function all()
    {
        return static::where('enabled', true)->get();
    }
}
