<?php

namespace App;

class Address extends BaseModel
{
//    protected $with = ['areaCode', 'customers'];
//    protected $with = ['customers'];

    protected $attributes = ['address_country', 'address_region'];

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

    public function getAddressCountryAttribute()
    {
        return $this->country->iso2_code;
    }

    public function getAddressRegionAttribute()
    {
        return $this->region_id ?? $this->region_name;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    protected function region_id()
    {
        return $this->belongsTo(Region::class, 'region', 'region_id');
    }

    protected function region_name()
    {
        return $this->belongsTo(Region::class, 'region', 'default_name');
    }
}
