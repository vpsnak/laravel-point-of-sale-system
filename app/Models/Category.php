<?php

namespace App;

class Category extends BaseModel
{
    //    protected $with = ['products'];

    protected $fillable = [
        'name',
        'in_product_listing'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
