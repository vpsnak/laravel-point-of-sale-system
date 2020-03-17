<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Money\Currency;
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
        return new Money($price['amount'], new Currency($price['currency']));
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
        $this->belongsTo(User::class);
    }
}
