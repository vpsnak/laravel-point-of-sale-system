<?php

namespace App;

class OrderAddress extends BaseModel
{
    protected $with = ['country', 'region_id'];

    protected $fillable = [
        'first_name',
        'last_name',
        'street',
        'street2',
        'city',
        'country_id',
        'region',
        'postcode',
        'phone',
        'company',
        'vat_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    protected function region_id()
    {
        return $this->belongsTo(Region::class, 'region', 'region_id');
    }
}
