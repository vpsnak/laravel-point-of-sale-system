<?php

namespace App;

class AreaCode extends BaseModel
{
    protected $with = ['addresses'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
