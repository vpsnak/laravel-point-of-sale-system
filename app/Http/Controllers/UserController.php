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

        $response = $http->post('localhost/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'wtx0OMH1wmzMjVu8KV72vC6QXDqijRrBJygHJRV7',
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'scope' => '',
            ],
        ]);

        $user = User::whereEmail($validatedData['username'])->firstOrFail();
        $token = (json_decode((string) $response->getBody(), true))['access_token'];

        return response([
            'notification' => [
                'msg' => 'Welcome ' . $user->name,
                'type' => 'info'
            ],
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        return response([
            'notification' => [
                'type' => 'success',
                'msg' => '<s>B</s><strong>y</strong><i>e</i> . . .'
            ]
        ], 200);
    }
}
