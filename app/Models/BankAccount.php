<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'account'
    ];

    protected $casts = [
        'account' => 'array',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
