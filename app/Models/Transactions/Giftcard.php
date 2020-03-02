<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Giftcard extends Model
{
    protected $fillable = [
        'name',
        'code',
        'enabled',
        'price',
    ];

    protected $casts = [
        'price' => 'array',
        'enabled' => 'datetime:m/d/Y H:i:s',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function setPriceAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->attributes['price'] = $value;
    }
}
