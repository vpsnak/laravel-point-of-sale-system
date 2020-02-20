<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;

    protected $with = ['country'];

    protected $fillable = [
        'country_id',
        'code',
        'name',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function store_pickups()
    {
        return $this->hasMany(StorePickup::class);
    }
}
