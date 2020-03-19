<?php

namespace App;

use App\Helper\Price;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class Giftcard extends Model
{
    protected $fillable = [
        'name',
        'code',
        'enabled_at',
        'price',
        'created_by_id'
    ];

    protected $casts = [
        'enabled_at' => 'datetime:m/d/Y H:i:s',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

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

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
