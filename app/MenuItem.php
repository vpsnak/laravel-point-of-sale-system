<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class MenuItem extends Model
{
    public function getByRole(Role $role, string $location)
    {
        return self::where()->get();
    }

    public function setActionAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['action'] = json_encode($value);
        } else {
            $this->attributes['action'] = $value;
        }
    }

    protected $fillable = [
        'name',
        'icon',
        'action',
        'location'
    ];

    protected $casts = [
        'action' => 'array',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
