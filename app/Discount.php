<?php

namespace App;

class Discount extends BaseModel
{
    protected $fillable = [
        'type',
        'amount',
    ];
    
    public function coupon()
    {
        return $this->hasOne(Coupon::class);
    }
}
