<?php

namespace App;

class Store extends BaseModel
{
    protected $with = ['cash_registers'];
    protected $fillable = [
        'name',
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
}
