<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasAccount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'default_recipient_account',
        'default_online_partner_account',
        'coupon_code',
        'giftcard_code'
    ];

    protected $casts = [
        'active' => 'boolean',
        'default_recipient_account' => 'array',
        'default_online_partner_account' => 'array',
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

    public function setDefaultRecipientAccount($value)
    {
        if (!is_string($value)) {
            $this->attributes['default_recipient_account'] = json_encode($value);
        } else {
            $this->attributes['default_recipient_account'] = $value;
        }
    }

    public function setDefaultOnlinePartnerAccount($value)
    {
        if (!is_string($value)) {
            $this->attributes['default_online_partner_account'] = json_encode($value);
        } else {
            $this->attributes['default_online_partner_account'] = $value;
        }
    }
}
