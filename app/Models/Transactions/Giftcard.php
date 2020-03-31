<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class Giftcard extends Model
{
    protected $appends = [
        'type'
    ];

    protected $fillable = [
        'name',
        'code',
        'enabled_at',
        'price',
        'original_price',
        'created_by_id'
    ];

    protected $casts = [
        'enabled_at' => 'datetime:m/d/Y H:i:s',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function getTypeAttribute()
    {
        return 'giftcard';
    }

    public function getPriceAttribute()
    {
        $price = json_decode($this->attributes['price'], true);
        return Price::parsePrice($price);
    }

    public function setPriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $this->attributes['price'] = json_encode($value);
        } else {
            $this->attributes['price'] = $value;
        }
    }

    public function getOriginalPriceAttribute()
    {
        $original_price = json_decode($this->attributes['original_price'], true);
        return Price::parsePrice($original_price);
    }

    public function setOriginalPriceAttribute($value)
    {
        if (is_array($value) || $value instanceof Money) {
            $this->attributes['original_price'] = json_encode($value);
        } else {
            $this->attributes['original_price'] = $value;
        }
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
