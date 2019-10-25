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

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function elavonApiPayments()
    {
        return $this->hasMany(ElavonApiPayment::class);
    }
}
