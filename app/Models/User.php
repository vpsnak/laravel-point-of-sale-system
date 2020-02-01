<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'api';

    protected $with = ['open_register', 'roles'];
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
        'password'
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
        'email_verified_at' => 'datetime',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    // public function getCreatedAtAttribute($value)
    // {
    //     return $value ? Carbon::parse($value)->format(config('models.format.timestamp')) : null;
    // }

    // public function getUpdatedAtAttribute($value)
    // {
    //     return $value ? Carbon::parse($value)->format(config('models.format.timestamp')) : null;
    // }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function openRegister()
    {
        return $this->hasOne(CashRegisterLogs::class);
    }

    public function open_register()
    {
        return $this->openRegister()->whereStatus(1);
    }

    public function findForPassport($username)
    {
        return $this->orWhere('email', $username)->orWhere('username', $username)->orWhere('phone', $username)->first();
    }
}
