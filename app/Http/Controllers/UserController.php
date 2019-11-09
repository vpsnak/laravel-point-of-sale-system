<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $model = User::class;

    public function all()
    {
        return response($this->model::paginate(), 200);
    }

    public function get($id)
    {
        return response($this->model::find($id), 200);
    }

    public function delete(User $user)
    {
        return response($user->delete(), 200);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $http = new Client;
        $user = User::whereEmail($validatedData['username'])->first();

        if (!$user) {
            if (Hash::verify($validatedData['password'], $user->password))
                return response(['errors' => [
                    'Login' => 'Invalid credentials',
                ]], 422);
        } else {
            $role = ($user->roles)[0]['name'];
            $response = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '2',
                    'client_secret' => 'wtx0OMH1wmzMjVu8KV72vC6QXDqijRrBJygHJRV7',
                    'username' => $validatedData['username'],
                    'password' => $validatedData['password'],
                    'scope' => $role,
                ],
            ]);

            $token = (json_decode((string) $response->getBody(), true))['access_token'];

            return response([
                'info' => ['Login' => 'Welcome <strong>' . $user->name . '</strong>'],
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response(['info' => ['Auth' => 'Password changed successfully!']]);
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        return response(['info' => ['Logout' => 'Goodbye...']], 200);
    }
}
