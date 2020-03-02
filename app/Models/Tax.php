<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'percentage',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
