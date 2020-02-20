<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorePickup extends Model
{
    protected $fillable = [
        'name',
        'street',
        'street1',
        'region_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'region_id' => 'integer',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
