<?php

namespace App;

class Product extends BaseModel
{
    protected $appends = ['final_price', 'stock'];

    protected $with = ['stores', 'price', 'categories'];

    public function getFinalPriceAttribute()
    {
        return $this->price->amount;
    }

    public function getStockAttribute()
    {
        $stock = 0;
        foreach ($this->stores as $store) {
            $stock += $store->pivot->qty;
        };
        return $stock;
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('qty');
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class)->withPivot('qty');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function price()
    {
        return $this->morphOne('App\Price', 'priceable');
    }
}
