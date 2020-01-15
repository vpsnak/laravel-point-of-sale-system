<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'store_id',
        'type',
        'account'
    ];

    protected $casts = [
        'account' => 'array'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
