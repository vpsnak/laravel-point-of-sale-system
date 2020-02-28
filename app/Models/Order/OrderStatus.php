<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderStatus extends Pivot
{
    public function processedOn()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
