<?php

namespace App;

class PaymentType extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'is_default',
        'created_by',
    ];
}
