<?php

namespace App;

class Tax extends BaseModel
{
    protected $fillable = [
        'name',
        'percentage',
        'is_default',
    ];

    protected $casts = [
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];
}
