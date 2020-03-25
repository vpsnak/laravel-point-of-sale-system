<?php

namespace App\Http\Controllers;

use App\Role;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Str;

class UserController extends Controller
{
    public function all()
    {
        return response(User::with('roles')->paginate());
    }

    public function getOne(User $model)
    {
        return response($model->load('roles'));
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $http = new Client;
        $url = config('app.url') . '/oauth/token';

        $response = $http->post($url, [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'wtx0OMH1wmzMjVu8KV72vC6QXDqijRrBJygHJRV7',
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'scope' => User::getRoleNameByIndentifier($validatedData['username']),
            ],
        ]);

        $user =
            User::findForPassport($validatedData['username'])
            ->load('settings', 'roles');
        $token = (json_decode((string) $response->getBody(), true))['access_token'];

        return response([
            'notification' => [
                'msg' => ["Welcome <b>{$user->name}</b>!"],
                'type' => 'info'
            ],
            'user' => $user,
            'token' => "Bearer $token"
        ]);
    }

    public function changeSelfPwd(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user->verifyPwd($validatedData['current_password'])) {
            $user->password = $validatedData['password'];
            $user->save();

            return response([
                'notification' => [
                    'msg' => ['Password changed successfully!'],
                    'type' => 'success'
                ]
            ]);
        } else {
            return response(['errors' => ['Current password is incorrect']], 422);
        }
    }

    public function verifySelfPwd(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string'
        ]);

        $user = auth()->user();

        if ($user->verifyPwd($validatedData['current_password'])) {
            return response(['notification' => [
                'msg' => ['Verified!'],
                'type' => 'success'
            ]]);
        } else {
            return response(['errors' => ['Password verification failed']], 500);
        }
    }

    public function changeUserPwd(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $user->password = $validatedData['password'];
        $user->save();

        return response(['notification' => [
            'msg' => ['Password changed successfully!'],
            'type' => 'success'
        ]]);
    }

    public function logout(User $user = null)
    {
        $targetUser = $user ? $user : auth()->user();

        if ($targetUser->open_register) {
            $targetUser->open_register->user_id = null;
            $targetUser->open_register->save();
        }

        $targetUser->token()->delete();

        return response(['notification' => [
            'msg' => ['Goodbye...'],
            'type' => "info"
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['username', 'name', 'email', 'phone'];
        $query = User::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15|min:10',
            'active' => 'required|boolean',
            'role_id' => 'required|exists:roles,id'
        ]);
        $validatedData['password'] = Str::random(16);
        $role = Role::findOrFail($validatedData['role_id']);
        unset($validatedData['role_id']);
        $user = User::create($validatedData);
        $roleController = new RoleController($user, $role);
        $roleController->assignRole(false, false);

        Setting::create([
            'key' => 'dark_mode',
            'value' => false,
            'user_id' => $user->id
        ]);

        return response(['notification' => [
            'msg' => ["User {$user->name} created successfully!"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'active' => 'required|boolean',
            'role_id' => 'required|exists:roles,id'
        ]);
        $role = Role::findOrFail($validatedData['role_id']);
        unset($validatedData['role_id']);
        $user = User::findOrFail($validatedData['id']);
        $user->update($validatedData);
        $roleController = new RoleController($user, $role);
        $roleController->assignRole();

        Setting::createUserDefaultSettings($user->id);

        return response(['notification' => [
            'msg' => ["User {$user->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }
}
