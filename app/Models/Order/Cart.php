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

    public function canDelete($user_id)
    {
        return $this->created_by_id === $user_id ? true : false;
    }

    public static function getUserPublicCarts(int $created_by_id)
    {
        return
            Cart::orWhere('created_by_id', $created_by_id)
            ->orWhere('is_public', true);
    }

    public function cashRegister()
    {
        $this->belongsTo(CashRegister::class);
    }

    public function createdBy()
    {
        $this->belongsTo(User::class);
    }
}
