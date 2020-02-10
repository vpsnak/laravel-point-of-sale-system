<?php

namespace App;

use Spatie\Permission\Models\Role as SpatieRole;

use App\MenuItem;

class Role extends SpatieRole
{
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class);
    }
}
