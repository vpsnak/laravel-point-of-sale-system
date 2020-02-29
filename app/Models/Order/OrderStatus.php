<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderStatus extends Pivot
{
    public $timestamps = ['created_at'];

    protected $with = [
        'processedBy'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
    ];

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
