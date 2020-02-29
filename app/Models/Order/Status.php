<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;

class Status extends Model
{
    use EagerLoadPivotTrait;
    public $timestamps = false;

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->using(OrderStatus::class)
            ->withPivot('user_id')
            ->withTimestamps(['created_at']);
    }

    protected $casts = [
        'lock_order' => 'boolean',
    ];
}
