<?php

namespace App;

class Address extends BaseModel
{
//    protected $with = ['areaCode', 'customers'];
//    protected $with = ['customers'];
    protected $fillable = [
        'first_name',
        'last_name',
        'street',
        'city',
        'county_id',
        'region',
        'postcode',
        'phone',
    ];

    public function areaCode()
    {
        return $this->hasOne(AreaCode::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }
}
