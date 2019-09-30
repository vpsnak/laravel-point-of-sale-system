<?php

namespace App;

class Giftcard extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'enabled',
        'amount',
    ];
}
