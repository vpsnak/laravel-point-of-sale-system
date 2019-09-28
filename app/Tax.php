<?php

namespace App;

class Tax extends BaseModel
{
    protected $fillable = [
        'name',
        'percentage',
        'is_default',
    ];
}
