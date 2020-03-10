<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public $timestamps = false;

    protected $with = [
        'company',
        'tax',
    ];

    protected $fillable = [
        'name',
        'phone',
        'street',
        'postcode',
        'city',
        'taxable',
        'is_default',
        'active',
        'is_phone_center',
        'company_id',
        'tax_id',
        'default_currency'
    ];

    protected $casts = [
        'is_phone_center' => 'boolean',
        'active' => 'boolean'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
