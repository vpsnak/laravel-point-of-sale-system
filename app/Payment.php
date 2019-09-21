<?php

namespace App;

class Payment extends BaseModel
{
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
