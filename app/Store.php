<?php

namespace App;

class Store extends BaseModel
{
//    protected $with = ['products'];
    protected $fillable = [
        'name',
        'taxable',
        'is_default',
        'tax_id',
        'created_by',
    ];

    public function products()
    {
        $this->belongsToMany(Product::class);
    }
}
