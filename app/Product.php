<?php

namespace App;

class Product extends BaseModel
{
    protected $appends = ['final_price', 'stock', 'magento_stock', 'laravel_stock'];

    protected $with = ['stores', 'price', 'categories'];

    protected $fillable = [
        'magento_id',
        'stock_id',
        'sku',
        'name',
        'barcode',
        'photo_url',
        'url',
        'description',
    ];

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

    public function getMagentoStockAttribute()
    {
        foreach ($this->stores as $store) {
            if ($store->id == 2) {
                return $store->pivot->qty;
            }
        }
        return null;
    }

    public function getLaravelStockAttribute()
    {
        foreach ($this->stores as $store) {
            if ($store->id == 1) {
                return $store->pivot->qty;
            }
        }
        return null;
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
