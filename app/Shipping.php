<?php

namespace App;

class Shipping extends BaseModel
{
    public function price()
    {
        return $this->morphOne(Price::class, 'priceable');
    }
}
