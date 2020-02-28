<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderStatus extends Pivot
{
    // public $timestamps = ['processed_on'];

    protected $with = [
        'processedBy'
    ];

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
