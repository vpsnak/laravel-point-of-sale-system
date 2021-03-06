<?php

namespace App\Http\Controllers;

use App\Events\UserDeauth;
use App\Role;
use App\Setting;
use App\User;
use DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Str;

class UserController extends Controller
{
    private $user;

    public function all()
    {
        return response(User::with('roles')->paginate(10));
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

        $user =
            User::getUserByIndentifier($validatedData['username'])
            ->with(['settings', 'roles'])
            ->first();

        $response = $http->post($url, [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'wtx0OMH1wmzMjVu8KV72vC6QXDqijRrBJygHJRV7',
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'scope' => $user->role->name,
            ],
        ]);



        $token = (json_decode((string) $response->getBody(), true))['access_token'];

        return response([
            'notification' => [
                'msg' => ["Welcome <b>{$user->name}</b>"],
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
            'deauth' => 'required|boolean'
        ]);
        if ($validatedData['user_id'] !== auth()->user()->id) {
            $user = User::findOrFail($validatedData['user_id']);
            $user->password = $validatedData['password'];
            $user->save();

            if ($validatedData['deauth']) {
                $this->deauthUser($user);
            }

            return response(['notification' => [
                'msg' => ['Password changed successfully!'],
                'type' => 'success'
            ]]);
        } else {
            return response(['errors' => [""]], 422);
        }
    }

    public function deauthUser(User $model)
    {
        if ($model->id !== auth()->user()->id) {
            $msg = 'You\'ve been signed out';
            $this->logout($model, true, $msg);

            return response(['notification' => [
                'msg' => "User {$model->name} has been deauthorized",
                'type' => 'success'
            ]]);
        } else {
            return response(['errors' => ["You cannot deauth yourself"]], 422);
        }
    }

    public function logout(User $user = null, bool $deauthEvent = false, string $deauthMsg = null)
    {
        if ($user) {
            $this->user = $user;
        } else if (!$this->user) {
            $this->user = auth()->user();
        }

        if ($this->user->open_register) {
            $this->user->open_register->user_id = null;
            $this->user->open_register->save();
        }

        // remove user's tokens
        $tokens = DB::table('oauth_access_tokens')->where('user_id', $this->user->id)->get();
        if ($tokens) {
            foreach ($tokens as $token) {
                DB::table('oauth_refresh_tokens')->where('access_token_id', $token->id)->delete();
            }
            DB::table('oauth_access_tokens')->where('user_id', $this->user->id)->delete();
        }

        if ($deauthEvent) {
            $msg = $deauthMsg;
            event(new UserDeauth($this->user, $msg));
        } else {
            return response(['notification' => [
                'msg' => ['Goodbye...'],
                'type' => "info"
            ]]);
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['username', 'name', 'email', 'phone'];
        $query = User::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate(10));
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

        Setting::createUserDefaultSettings($user->id);

        return response(['notification' => [
            'msg' => ["User {$user->name} created successfully"],
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
        $this->user = User::findOrFail($validatedData['id']);
        $this->user->update($validatedData);

        if (!$this->user->role || $this->user->role->id !== $role->id) {
            $roleController = new RoleController($this->user, $role);
            $roleController->assignRole();
        } else if (!$this->user->active) {
            $msg = 'Your account has been deactivated';
            $this->logout(null, true, $msg);
        }

        return response(['notification' => [
            'msg' => ["User {$this->user->name} updated successfully"],
            'type' => 'success'
        ]]);
    }
}
