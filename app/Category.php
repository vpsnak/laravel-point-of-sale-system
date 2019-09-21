<?php

namespace App;

class Category extends BaseModel
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
