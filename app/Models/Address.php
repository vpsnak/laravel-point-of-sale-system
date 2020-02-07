<?php

namespace App;

class Address extends BaseModel
{
    //    protected $with = ['areaCode', 'customers'];
    //    protected $with = ['customers'];

    protected $appends = ['address_country', 'address_region'];

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
        'billing',
        'shipping',
        'location',
        'location_name'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
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
        if (!empty($this->country)) {
            return $this->country->iso2_code;
        }
        return $this->country_id;
    }

    public function getAddressRegionAttribute()
    {
        return ($this->region_id ?? $this->region_name) ?? $this->region;
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
