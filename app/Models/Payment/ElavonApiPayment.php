<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonApiPayment extends Model
{
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
