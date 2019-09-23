<?php

namespace App;

class Shipping extends BaseModel
{
    protected $with = ['price'];

    public function price()
    {
        return $this->morphOne(Price::class, 'priceable');
    }
}
