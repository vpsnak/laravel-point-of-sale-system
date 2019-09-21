<?php

namespace App;

class Product extends BaseModel
{
    public function altSku()
    {
        return $this->sku .= 'asd';
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('qty');
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class)->withPivot('qty');
    }

    public function price()
    {
        return $this->morphOne('App\Price', 'priceable');
    }

    public static function allData()
    {
        return get_called_class()::with(['stores', 'price'])->get();
    }
}
