<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function payments()
    {
        return $this->belongsToMany(App\Payment::class);
    }
}
