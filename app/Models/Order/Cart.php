<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'cash_register_id',
        'name',
        'cart',
        'is_globally_visible',
        'created_by_id'
    ];

    protected $casts = [
        'is_globally_visible' => 'boolean',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $hidden = [
        'created_by_id',
        'cash_register_id',
        'is_globally_visible'
    ];

    public function createdBy()
    {
        $this->belongsTo(User::class);
    }
}
