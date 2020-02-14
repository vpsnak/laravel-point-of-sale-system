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
        'is_default_billing',
        'is_default_shipping',
        'location',
        'location_name',
        'customer_id'
    ];

    protected $casts = [
        'is_default_billing' => 'boolean',
        'is_default_shipping' => 'boolean',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    protected $hidden = [
        'magento_id'
    ];

    public function setIsDefaultShippingAttribute($value)
    {
        if ($customer = $this->customer) {
            $currentCustomerShipping = $customer->addresses->where('is_default_shipping', 1)->first();
            if ($currentCustomerShipping && $value) {
                $currentCustomerShipping->is_default_shipping = false;
                $currentCustomerShipping->save();
            }
        }
        $this->attributes['is_default_shipping'] = $value;
    }

    public function setIsDefaultBillingAttribute($value)
    {
        if ($customer = $this->customer) {
            $currentCustomerBilling = $customer->addresses->where('is_default_billing', 1)->first();
            if ($currentCustomerBilling && $value) {
                $currentCustomerBilling->is_default_billing = false;
                $currentCustomerBilling->save();
            }
        }
        $this->attributes['is_default_billing'] = $value;
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
