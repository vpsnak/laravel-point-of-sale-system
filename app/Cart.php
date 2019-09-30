<?php

namespace App;

class Cart extends BaseModel
{
    protected $fillable = [
        'cash_register_id',
        'name',
        'cart',
    ];
}
