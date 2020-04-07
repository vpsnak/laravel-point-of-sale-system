<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'data',
        'user_id'
    ];

    protected $hidden = [
        'user_id'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setDataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['data'] = json_encode($value);
        } else {
            $this->attributes['data'] = $value;
        }
    }

    public static function createUserDefaultSettings(int $user_id)
    {
        Setting::create([
            'key' => 'theme_dark',
            'value' => true,
            'user_id' => $user_id
        ]);
    }

    // get individual settings

    // settings per user
    public static function getUserThemeDark(int $user_id)
    {
        return
            self
            ::where('user_id', $user_id)
            ->where('key', 'theme_dark')
            ->firstOrFail();
    }

    // global settings
}
