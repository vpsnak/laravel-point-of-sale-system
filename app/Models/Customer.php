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
        'phone'
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    protected $with = ['addresses'];
    protected $appends = ['full_name'];

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
