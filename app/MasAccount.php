<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasAccount extends Model
{
    public $timestamps = false;

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function getActive()
    {
        return self::whereActive(true)->firstOrFail();
    }

    public function getAuthHeader()
    {
        return 'Basic ' . base64_encode($this->username . ':' . $this->password . ':' . $this->direct_id);
    }

    public function setFulfillerMdNumberAttribute($value)
    {
        $this->attributes['fulfiller_md_number'] = encrypt($value);
    }

    public function getFulfillerMdNumberAttribute($value)
    {
        return decrypt($value);
    }

    public function setDirectIdAttribute($value)
    {
        $this->attributes['direct_id'] = encrypt($value);
    }

    public function getDirectIdAttribute($value)
    {
        return decrypt($value);
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = encrypt($value);
    }

    public function getUsernameAttribute($value)
    {
        return decrypt($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }

    public function getPasswordAttribute($value)
    {
        return decrypt($value);
    }
}
