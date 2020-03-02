<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'code',
        'uses',
        'from',
        'to',
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s',
        'from' => 'm/d/Y',
        'to' => 'm/d/Y',
    ];

    protected $dates = ['from', 'to'];
}
