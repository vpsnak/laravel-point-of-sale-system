<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
