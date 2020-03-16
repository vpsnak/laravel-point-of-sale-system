<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'tax_number',
        'logo'
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
        return $this->bankAccounts()->whereType('sdk')->firstOrFail();
    }

    public function bankAccountApi()
    {
        return $this->bankAccounts()->whereType('api')->firstOrFail();
    }
}
