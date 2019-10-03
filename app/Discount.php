<?php

namespace App;

class Discount extends BaseModel
{
    public function coupon()
    {
        return $this->hasOne(Coupon::class);
    }
}
