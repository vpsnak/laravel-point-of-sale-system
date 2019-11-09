<?php

namespace App;

class CashRegister extends BaseModel
{
    protected $appends = ['is_open', 'store', 'payments'];
    protected $with = [
//        'logs',
//        'payments'
    ];

    protected $fillable = [
        'name',
        'store_id',
        'created_by',
        'barcode',
        'pos_terminal',
        'printer',
    ];

    public function getStoreAttribute()
    {
        return Store::getOne($this->store_id);
    }

    public function getIsOpenAttribute()
    {
        if (!empty($this->getOpenLog())) {
            return true;
        }
        return false;
    }

    public function getOpenLog()
    {
        return $this->hasMany(CashRegisterLogs::class)->whereStatus(1)->first();
    }

    public function getPaymentsAttribute()
    {
        return $this->hasMany(Payment::class, 'cash_register_id', 'id')->without(['order', 'created_by'])->get();
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
