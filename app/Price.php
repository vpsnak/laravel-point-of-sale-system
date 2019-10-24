<?php

namespace App;

class Price extends BaseModel
{
    protected $fillable = [
        'amount'
    ];

    public function priceable()
    {
        return $this->morphTo();
    }
}
