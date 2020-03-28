<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $appends = [
        'can_delete'
    ];

    protected $fillable = [
        'name',
        'cart',
        'visibility',
        'store_id',
        'created_by_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    protected $hidden = [
        'created_by_id',
        'store_id',
        'visibility'
    ];

    public function getCanDeleteAttribute(int $user_id)
    {
        return $this->created_by_id === $user_id ? true : false;
    }

    public static function getUserCarts(User $user)
    {
        $cash_register =  $user->open_register->cash_register;
        $store = $cash_register->store;
        return
            Cart::orWhere('created_by_id', $user->id)
            ->orWhere('visibility', 'public')
            ->orWhere(function ($query) use ($store) {
                $query
                    ->where('visibility', 'store')
                    ->where('store_id', $store->id);
            });
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
