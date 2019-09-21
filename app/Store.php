<?php

namespace App;

class Store extends BaseModel
{
    public function products()
    {
        $this->belongsToMany(Product::class);
    }
}
