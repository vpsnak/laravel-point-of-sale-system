<?php

namespace App;

class Customer extends BaseModel
{
    protected $fillable = [
        'magento_id',
        'first_name',
        'last_name',
        'email',
        'house_account_status',
        'house_account_number',
        'house_account_limit',
        'no_tax',
        'no_tax_file',
        'comment',
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    protected $with = ['addresses'];

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }
}
