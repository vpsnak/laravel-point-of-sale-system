<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorePickup extends Model
{
    protected $fillable = [
        'name',
        'address_id'
    ];

    protected $casts = [
        'region_id' => 'integer',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
