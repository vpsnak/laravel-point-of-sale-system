<?php

namespace App;

class Store extends BaseModel
{
    protected $with = [
        'company',
        'tax'
    ];

    protected $fillable = [
        'name',
        'phone',
        'street',
        'postal_code',
        'city',
        'taxable',
        'is_default',
        'tax_id',
        'created_by',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function cash_registers()
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
