<?php

namespace App;

class Payment extends BaseModel
{
    protected $with = ['orders'];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
