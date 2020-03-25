<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\User;
use DB;


class RoleController extends Controller
{
    private $user;
    private $role;

    public function __construct(User $user = null, Role $role = null)
    {
        $this->user = $user;
        $this->role = $role;
    }

    public function all()
    {
        return response(Role::all());
    }

    public function assignRole(bool $replace = true, bool $deauth = true, User $user = null, Role $role = null)
    {
        $this->user = $user ? $user : $this->user;
        $this->role = $role ? $role : $this->role;

        if ($replace) {
            $this->removeRoles($user);
        }

        // apply user role
        $this->user = $this->user->assignRole($this->role);

        if ($deauth) {
            $userController = new UserController();
            $msg = 'Your account role has changed<br>Please login again';
            $userController->logout($this->user, true, $msg);
        }
    }

    public function removeRoles(User $user = null)
    {
        $this->user = $user ? $user : $this->user;
        if ($this->user->roles) {
            foreach ($this->user->roles as $role) {
                $this->user->removeRole($role->name);
            }
        }
    }
}
