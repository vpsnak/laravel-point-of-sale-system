<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'api';

    protected $appends = [
        'role_name'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'active' => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getRoleAttribute()
    {
        return $this->roles[0] ?? null;
    }

    public function getRoleNameAttribute()
    {
        return $this->role->text ?? null;
    }

    public static function activeUsers()
    {
        return self::whereActive(true);
    }

    public function verifyPwd(string $password)
    {
        return Hash::check($password, $this->password);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orderStatuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'created_by_id');
    }

    public function openRegister()
    {
        return $this->hasOne(CashRegisterLogs::class);
    }

    public function open_register()
    {
        return $this->openRegister()->whereStatus(1);
    }

    public static function getUserByIndentifier(string $identifier)
    {
        return
            self::where('active', true)
            ->where(function ($query) use ($identifier) {
                $query
                    ->orWhere('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->orWhere('phone', $identifier);
            });
    }

    public static function findForPassport(string $identifier)
    {
        return self::getUserByIndentifier($identifier)->first();
    }
}
