<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'account'
    ];

    protected $casts = [
        'account' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
