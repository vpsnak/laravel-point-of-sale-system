<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestEmailList extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email'
    ];
}
