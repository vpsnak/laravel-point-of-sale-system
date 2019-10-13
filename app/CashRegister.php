<?php

namespace App;

class CashRegister extends BaseModel
{
    protected $with = [
//        'parent_store',
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

    public function parent_store()
    {
        return $this->belongsTo(Store::class, 'id', 'store_id');
    }
}
