<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'logo',
    ];

    public function stores()
    {
        return $this->hasMany(Company::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function bankAccountSdk()
    {
        return $this->bankAccounts()->whereType('sdk')->first();
    }

    public function bankAccountApi()
    {
        return $this->bankAccounts()->whereType('api')->first();
    }
}
