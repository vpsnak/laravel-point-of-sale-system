<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'cash_register_id',
        'name',
        'cart',
    ];
}
