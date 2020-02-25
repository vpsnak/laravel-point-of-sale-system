<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
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
}
