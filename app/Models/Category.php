<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
