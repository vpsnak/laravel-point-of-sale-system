<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function products()
    {
        $this->belongsToMany(App\Product::class);
    }
}
