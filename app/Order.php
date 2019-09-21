<?php

namespace App;

class Order extends BaseModel
{
    public function payments()
    {
        return $this->belongsToMany(Payment::class);
    }
}
