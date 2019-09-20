<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    public function price()
    {
        return $this->morphOne(\App\Price::class, 'priceable');
    }
}
