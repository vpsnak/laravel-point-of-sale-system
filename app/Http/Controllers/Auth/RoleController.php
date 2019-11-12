<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\User;


class RoleController extends Controller
{
    public function all()
    {
        return response(Role::all());
    }

    public function setRole(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($user->roles) {
            foreach ($user->roles as $assignedRole) {
                $user->removeRole($assignedRole->name);
            }
        }

        $role = Role::findOrFail($validatedData['role_id']);
        $user->assignRole($role->name);

        return response([
            'info' => ['Auth' => 'Role ' . $role->name . ' assigned to ' . $user->name . ' successfully!']
        ]);
    }
}
