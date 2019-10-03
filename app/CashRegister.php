<?php

namespace App;

class CashRegister extends BaseModel
{
    protected $with = [
        'logs'
    ];
    
   protected $fillable = [
        'name',
        'store_id',
        'created_by'
   ];
    
    public function logs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }
}
