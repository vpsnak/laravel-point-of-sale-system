<?php

namespace App;

class Category extends BaseModel
{
    //    protected $with = ['products'];
    
    protected $fillable = [
        'name',
        'in_product_listing'
    ];
    
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
    public static function allData()
    {
        return get_called_class()::with(['products'])->get();
    }
}
