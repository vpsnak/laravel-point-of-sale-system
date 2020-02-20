<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $with = [
        'company',
        'tax'
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
        'company_id',
        'tax_id',
        'user_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
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
