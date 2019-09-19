<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function areaCode()
    {
        return $this->hasOne(App\AreaCode::class);
    }

    public function customers()
    {
        return $this->belongsToMany(App\Customer::class);
    }
}
