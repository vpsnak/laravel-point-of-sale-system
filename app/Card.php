<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'content',
        'title',
        'created_by'
    ];

    public function cardable() {
        return $this->morphTo();
    }
}
