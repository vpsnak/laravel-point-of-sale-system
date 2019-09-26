<?php

namespace App;

class Store extends BaseModel
{
//    protected $with = ['products'];
    protected $fillable = ['name', 'is_default', 'tax_id'];

    public function products()
    {
        $this->belongsToMany(Product::class);
    }
}
