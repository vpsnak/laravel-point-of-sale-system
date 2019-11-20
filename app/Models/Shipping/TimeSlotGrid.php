<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlotGrid extends Model
{
    protected $table = 'timeslotgrid';

    public function timeslot()
    {
        return $this->hasOne(DeliveryTimeSlot::class, 'entity_id', 'timeslot_id');
    }
}
