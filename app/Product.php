<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function altSku()
    {
        return $this->sku .= 'asd';
    }

    public function stores()
    {
        return $this->belongsToMany(\App\Store::class)->withPivot('qty');
    }
}
