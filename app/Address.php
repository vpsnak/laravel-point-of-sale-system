<?php

namespace App;

class Address extends BaseModel
{
//    protected $with = ['areaCode', 'customers'];
//    protected $with = ['customers'];
    protected $fillable = [
        'magento_id',
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
        'deliverydate',
    ];

    protected $hidden = [
        'magento_id'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }
}
