<?php

namespace App;

class Cart extends BaseModel
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
