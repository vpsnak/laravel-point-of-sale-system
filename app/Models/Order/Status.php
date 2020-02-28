<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public $timestamps = false;

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('processed_on')
            ->using(OrderStatus::class);
    }

    protected $casts = [
        'lock_order' => 'boolean',
    ];
}
