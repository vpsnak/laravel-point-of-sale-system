<?php

namespace App;

class Address extends BaseModel
{
    protected $with = ['areaCode', 'customers'];

    public function areaCode()
    {
        return $this->hasOne(AreaCode::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }
}
