<?php

namespace App;

class AreaCode extends BaseModel
{
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
