<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElavonApiPayment extends Model
{
    protected $fillable = [
        'txn_id',
        'card_number',
        'card_holder',
        'status',
        'transaction',
        'log',
        'payment_id',
    ];

    protected $casts = [

        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function getLogAttribute()
    {
        return json_decode($this->attributes['log'], true);
    }

    public function setLogAttribute($value)
    {
        if (!is_string($value)) {
            $this->attributes['log'] = json_encode($value);
        } else {
            $this->attributes['log'] = strip_tags($value);
        }
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
