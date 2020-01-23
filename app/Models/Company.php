<?php

namespace App;

class Company extends BaseModel
{
    protected $fillable = [
        'name',
        'tax_number',
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
