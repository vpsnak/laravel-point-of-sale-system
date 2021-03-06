<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'house_account_status',
        'house_account_number',
        'house_account_limit',
        'no_tax',
        'no_tax_file',
        'phone',

        'magento_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $appends = ['full_name'];

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDefaultBilling()
    {
        return $this->addresses->where('is_default_billing', true)->first();
    }

    public function getDefaultShipping()
    {
        return $this->addresses->where('is_default_shipping', true)->first();
    }
}
