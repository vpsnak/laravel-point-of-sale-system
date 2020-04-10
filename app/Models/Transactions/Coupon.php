<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'code',
        'uses',
        'from',
        'to',
        'discount'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s',
        'discount' => 'array',
        'from' => 'm/d/Y',
        'to' => 'm/d/Y',
    ];

    public function setDiscountAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->attributes['discount'] = $value;
    }
}
