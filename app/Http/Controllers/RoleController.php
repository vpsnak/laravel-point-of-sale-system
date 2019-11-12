<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\User;


class RoleController extends Controller
{
    public function all()
    {
        return response(Role::all());
    }

    public function setRole(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($validatedData['user_id']);

        if ($user->roles) {
            foreach ($user->roles as $assignedRole) {
                $user->removeRole($assignedRole->name);
            }
        }

        $role = Role::findOrFail($validatedData['role_id']);
        $user->assignRole($role->name);

        $user->token()->delete();

        return response([
            'info' => ['Auth' => 'Role ' . $role->name . ' assigned to ' . $user->name . ' successfully!']
        ]);
    }
}
