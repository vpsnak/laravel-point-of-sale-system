<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
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
