<?php

namespace App;

class Customer extends BaseModel
{
    protected $fillable = [
        'magento_id',
        'first_name',
        'last_name',
        'email'
    ];

    protected $with = ['addresses'];

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }
}
