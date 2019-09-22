<?php

namespace App;

class Product extends BaseModel
{
    protected $appends = ['final_price', 'stock'];
    
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
    
    public function price()
    {
        return $this->morphOne('App\Price', 'priceable');
    }
    
    public static function allData()
    {
        return get_called_class()::with(['stores', 'price'])->get();
    }
}
