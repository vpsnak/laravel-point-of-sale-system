<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    public function all()
    {
        return response(User::paginate());
    }

    public function get($model)
    {
        return response(User::findOrFail($model));
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $http = new Client;

        $response = $http->post(config('app.url') . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'wtx0OMH1wmzMjVu8KV72vC6QXDqijRrBJygHJRV7',
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'scope' => User::getRoleNameByIndentifier($validatedData['username']),
            ],
        ]);

        $user = User::findForPassport($validatedData['username']);
        $token = (json_decode((string) $response->getBody(), true))['access_token'];

        return response([
            'notification' => [
                'msg' => ["Welcome <b>{$user->name}</b>!"],
                'type' => 'success'
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

    public function logout()
    {
        $user = auth()->user();

        if ($user->open_register) {
            $user->open_register->user_id = null;
            $user->open_register->save();
        }

        $user->token()->delete();

        return response(['notification' => [
            'msg' => ['Goodbye...'],
            'type' => "success"
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
            'password' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'active' => 'required|boolean'
        ]);

        $user = User::create($validatedData);

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
            'active' => 'required|boolean'
        ]);
        $user = User::findOrFail($validatedData['id']);

        $user->fill($validatedData);
        $user->save();

        return response(['notification' => [
            'msg' => ["User {$user->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }
}
