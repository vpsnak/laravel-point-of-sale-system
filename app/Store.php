<?php

namespace App;

class Store extends BaseModel
{
//    protected $with = ['products'];

    public function products()
    {
        $this->belongsToMany(Product::class);
    }
}
