<?php

namespace App;

class StorePickup extends BaseModel
{
    protected $with = ['country', 'region'];

    protected $fillable = [
        'name',
        'street',
        'street1',
        'region_id',
        'country_id',
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }
}
