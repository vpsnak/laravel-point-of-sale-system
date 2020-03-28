<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const LARAVEL_STORE_ID = 1;
    const MAGENTO_STORE_ID = 2;

    protected $appends = ['stock', 'magento_stock', 'laravel_stock', 'original_price', 'type'];

    protected $with = ['stores', 'categories'];

    protected $fillable = [
        'sku',
        'name',
        'barcode',
        'photo_url',
        'url',
        'plantcare_pdf',
        'description',
        'is_price_editable',
        'price',
        'discount',
        'is_discountable',

        'magento_id',
        'stock_id',
    ];

    protected $casts = [
        'price' => 'array',
        'discount' => 'array',
        'is_discountable' => 'boolean',
        'is_price_editable' => 'boolean',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function getTypeAttribute($value)
    {
        return 'product';
    }

    public function setDiscountAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->attributes['discount'] = $value;
    }

    public function setPriceAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (isset($value['amount'])) {
            $value['amount'] = (int) $value['amount'];
        }

        $this->attributes['price'] = json_encode($value);
    }

    public function getOriginalPriceAttribute()
    {
        return $this->price;
    }

    public function getStockAttribute()
    {
        $stock = 0;
        foreach ($this->stores as $store) {
            if ((int) $store->id === self::MAGENTO_STORE_ID) {
                $stock += $store->pivot->qty;
            }
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
        return $this
            ->belongsToMany(Store::class)
            ->withPivot('qty');
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

    public function cards()
    {
        return $this->morphMany(Card::class, 'cardable');
    }
}
