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

    public function getMagentoStockAttribute()
    {
        $store = $this->magentoStore();
        if (!empty($store)) {
            return $store->pivot->qty;
        }
        return null;
    }

    public function magentoStore()
    {
        return $this->store_view(2);
    }

    public function store_view($store_id)
    {
        return $this->stores()->where('id', $store_id)->first();
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('qty');
    }

    public function getLaravelStockAttribute()
    {
        $store = $this->laravelStore();
        if (!empty($store)) {
            return $store->pivot->qty;
        }
        return null;
    }

    public function laravelStore()
    {
        return $this->store_view(1);
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
