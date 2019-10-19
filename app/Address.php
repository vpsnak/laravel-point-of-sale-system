<?php

namespace App;

class Address extends BaseModel
{
//    protected $with = ['areaCode', 'customers'];
//    protected $with = ['customers'];
    protected $fillable = [
        'magento_id',
        'area_code_id',
        'first_name',
        'last_name',
        'street',
        'city',
        'country_id',
        'region',
        'postcode',
        'phone',
        'company',
        'vat_id',
    ];

    protected $hidden = [
        'magento_id'
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
