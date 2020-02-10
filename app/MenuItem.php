<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;

class MenuItem extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'action',
        'location'
    ];

    protected $casts = [
        'action' => 'array',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function setActionAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['action'] = json_encode($value);
        } else {
            $this->attributes['action'] = $value;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
