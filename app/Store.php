<?php

namespace App;

class Store extends BaseModel
{
    protected $with = [
        'tax'
    ];

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

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
