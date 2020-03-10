<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'type',
        'account'
    ];

    protected $casts = [
        'account' => 'array'
    ];

    public function setAccountAttribute($value)
    {
        $this->attributes['account'] = encrypt($value);
    }

    public function getAccountAttribute($value)
    {
        return decrypt($value);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
