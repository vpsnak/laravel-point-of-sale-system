<?php

namespace App;

class CashRegister extends BaseModel
{
    protected $appends = ['is_open', 'store'];
    protected $with = [
//        'logs'
    ];

    protected $fillable = [
        'name',
        'store_id',
        'created_by'
    ];

    public function getStoreAttribute()
    {
        return Store::getOne($this->store_id);
    }

    public function getIsOpenAttribute()
    {
        return $this->hasMany(CashRegisterLogs::class)->whereStatus(1)->count() > 0;
    }

    public function logs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class);
    }

    public function elavonApiPayments()
    {
        return $this->hasMany(ElavonApiPayment::class);
    }

    public function elavonSdkPayments()
    {
        return $this->hasMany(ElavonSdkPayment::class);
    }
}
