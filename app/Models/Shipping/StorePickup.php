<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorePickup extends Model
{
    protected $fillable = [
        'name',
        'street',
        'street2',
        'city',
        'region_id',
        'postcode',
        'phone',
        'company',
        'location_id',
    ];
    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $with = ['region'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
