<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $with = ['open_register'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function open_register()
    {
        $openLog = $this->hasOne(CashRegisterLogs::class);
        if (empty($openLog)) {
            return false;
        }
        return $openLog->whereStatus(1);
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class);
    }

    // public function findForPassport($username)
    // {
    //     return $this->orWhere('email', $username)->orWhere('phone', $username)->first();
    // }
}
