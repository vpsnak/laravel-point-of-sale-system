<?php

namespace App;

class Customer extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone'
    ];

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }

    public static function allData()
    {
        return get_called_class()::with('addresses')->get();
    }

}
