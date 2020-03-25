<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $appends = [
        'created_by_name'
    ];

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
        'default_currency',
        'created_by_id'
    ];

    protected $casts = [
        'is_phone_center' => 'boolean',
        'active' => 'boolean'
    ];

    public function getCreatedByNameAttribute()
    {
        return $this->createdBy->name ?? null;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this
            ->belongsToMany(Product::class)
            ->withPivot('qty');
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
