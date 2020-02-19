<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'iso2_code',
        'iso3_code',
        'name',
    ];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
