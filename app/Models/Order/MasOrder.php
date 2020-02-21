<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasOrder extends Model
{
    protected $fillable = [
        'order_id',
        'mas_control_number',
        'mas_message_number',
        'status',
        'payload',
        'response',
    ];

    public static function getCreditCardType(string $cardScheme)
    {
        switch (strtolower($cardScheme)) {
            case 'mastercard':
                return 1;
            case 'visa':
                return 2;
            case 'discover':
                return 3;
            case 'american express':
                return 4;
            default:
                return null;
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function setPayloadAttribute($value)
    {
        if (!is_string($value)) {
            $this->attributes['payload'] = json_encode($value);
        } else {
            $this->attributes['payload'] = $value;
        }
    }

    public function setResponseAttribute($value)
    {
        if (!is_string($value)) {
            $this->attributes['response'] = json_encode($value);
        } else {
            $this->attributes['response'] = $value;
        }
    }
}
