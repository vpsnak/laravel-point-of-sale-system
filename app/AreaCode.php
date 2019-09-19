<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaCode extends Model
{
    public function addresses()
    {
        return $this->hasMany(App\Addresses::class);
    }
}
