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
        $this->user = $this->user->assignRole($this->role);

        if ($replace) {
            $this->removeRoles($user);
        }

        if ($deauth) {
            $userController = new UserController();
            $userController->logout($this->user);
        }

        // remove user's tokens
        $tokens = DB::table('oauth_access_tokens')->where('user_id', $this->user->id)->get();
        if ($tokens) {
            foreach ($tokens as $token) {
                DB::table('oauth_refresh_tokens')->where('access_token_id', $token->id)->delete();
            }
            DB::table('oauth_access_tokens')->where('user_id', $this->user->id)->delete();
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
