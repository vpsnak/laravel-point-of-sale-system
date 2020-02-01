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

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];
}
