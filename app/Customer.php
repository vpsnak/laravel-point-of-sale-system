<?php

namespace App;

class Customer extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name'

    ];

    protected $with = ['addresses'];

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }
}
