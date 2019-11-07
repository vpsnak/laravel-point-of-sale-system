<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    protected $model = User::class;

    public function all()
    {
        return response($this->model::all(), 200);
    }

    public function get($id)
    {
        return response($this->model::find($id), 200);
    }

    public function delete($id)
    {
        $deleted = $this->model::where('id', $id)->delete();
        return response($deleted, 200);
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
            return response(['errors' => [
                'Login' => '<h3>Invalid credentials</h3>',
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
                'info' => ['Login:' => '<h2>Welcome ' . $user->name . '</h2>'],
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        return response(['info' => ['Logout: ' => '<h2>Goodbye...</h2>']], 200);
    }
}
