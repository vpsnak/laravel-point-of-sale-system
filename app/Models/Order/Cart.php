<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $appends = [
        'item_count',
        'customer',
    ];

    protected $fillable = [
        'cart',
        'created_by_id'
    ];

    protected $casts = [
        'cart' => 'array',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $hidden = [
        'created_by_id'
    ];

    public function getItemCountAttribute()
    {
        return "name: {$this->id}";
    }

    public function getCustomerAttribute()
    {
        return "name: {$this->id}";
    }

    public function setCartAttribute(array $value)
    {
        $this->attributes['cart'] = json_encode($value);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
