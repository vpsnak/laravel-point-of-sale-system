<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function areaCode()
    {
        return $this->hasOne(AreaCode::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }
}
