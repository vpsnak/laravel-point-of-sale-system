<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public function priceable()
    {
        return $this->morphTo();
    }
}
