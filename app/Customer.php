<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email'
    ];

    public function addresses()
    {
        return $this->belongsToMany(App\Address::class);
    }
}
