<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostalGroup extends Model
{
    protected $table = 'postalgroup';

    public function timeslotGrid()
    {
        return $this->hasMany(TimeSlotGrid::class, 'postalgroup_id', 'postalgroup_id');
    }
}
