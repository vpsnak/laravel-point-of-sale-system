<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
       protected $fillable = [
        'name',
        'icon'
    ];

      protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];
}
