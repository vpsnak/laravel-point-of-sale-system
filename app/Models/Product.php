<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const LARAVEL_STORE_ID = 1;
    const MAGENTO_STORE_ID = 2;

    protected $appends = ['final_price', 'stock', 'magento_stock', 'laravel_stock', 'original_price'];

    protected $with = ['stores', 'price', 'categories'];

    protected $fillable = [
        'magento_id',
        'stock_id',
        'sku',
        'name',
        'barcode',
        'photo_url',
        'url',
        'plantcare_pdf',
        'description',
        'editable_price'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function getOriginalPriceAttribute()
    {
        return $this->price->amount;
    }

    public function getFinalPriceAttribute()
    {
        if (!empty($this->price->discount)) {
            return Price::calculateDiscount(
                $this->price->amount,
                $this->price->discount->type,
                $this->price->discount->amount
            );
        }
        return $this->price->amount;
    }

    public function getStockAttribute()
    {
        $stock = 0;
        foreach ($this->stores as $store) {
            if ($store->id == self::MAGENTO_STORE_ID) {
                continue;
            }
            $stock += $store->pivot->qty;
        };
        return $stock;
    }

    public function getMagentoStockAttribute()
    {
        $store = $this->store_view(self::MAGENTO_STORE_ID);
        if (!empty($store)) {
            return $store->pivot->qty;
        }
        return null;
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
        $store = $this->store_view(self::LARAVEL_STORE_ID);
        if (!empty($store)) {
            return $store->pivot->qty;
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
