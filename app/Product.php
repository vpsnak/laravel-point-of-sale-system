<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function altSku()
    {
        return $this->sku .= 'asd';
    }

    public function stores()
    {
        return $this->belongsToMany(\App\Store::class)->withPivot('qty');
    }

    public function carts()
    {
        return $this->belongsToMany(\App\Cart::class)->withPivot('qty');
    }

    public function price()
    {
        return $this->hasOne(\App\Price::class);
    }
}
