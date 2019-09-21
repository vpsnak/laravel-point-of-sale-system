<?php

namespace App;

class Cart extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'name',
        'data'
    ];
}
