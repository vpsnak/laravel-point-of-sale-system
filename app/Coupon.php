<?php

namespace App;

class Coupon extends BaseModel
{
    protected $with = ['discount'];
    
    protected $fillable = [
        'name',
        'code',
        'uses',
        'discount_id',
        'from',
        'to',
    ];
    
    protected $dates = ['from', 'to'];
    
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }
}
