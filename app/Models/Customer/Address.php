<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $with = ['region'];

    protected $fillable = [
        'first_name',
        'last_name',
        'street',
        'street2',
        'city',
        'region_id',
        'postcode',
        'phone',
        'company',
        'vat_id',
        'is_default_billing',
        'is_default_shipping',
        'location',
        'location_name',
        'customer_id',

        'magento_id'
    ];

    protected $casts = [
        'is_default_billing' => 'boolean',
        'is_default_shipping' => 'boolean',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $hidden = [
        'magento_id'
    ];

    public function setIsDefaultShippingAttribute($value)
    {
        if ($customer = $this->customer) {
            $defaultShippingAddresses = $customer->addresses()->where('is_default_shipping', 1)->get();
            if ($defaultShippingAddresses && $value) {
                foreach ($defaultShippingAddresses as $defaultShippingAddress) {
                    $defaultShippingAddress->is_default_billing = false;
                    $defaultShippingAddress->save();
                }
            }
        }
        $this->attributes['is_default_shipping'] = $value;
    }

    public function setIsDefaultBillingAttribute($value)
    {
        if ($customer = $this->customer) {
            $defaultBillingAddresses = $customer->addresses()->where('is_default_billing', 1)->get();
            if ($defaultBillingAddresses && $value) {
                foreach ($defaultBillingAddresses as $defaultBillingAddress) {
                    $defaultBillingAddress->is_default_billing = false;
                    $defaultBillingAddress->save();
                }
            }
        }
        $this->attributes['is_default_billing'] = $value;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
