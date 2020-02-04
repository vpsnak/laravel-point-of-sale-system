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
        $user = User::orWhere('email', $validatedData['username'])
            ->orWhere('username', $validatedData['username'])
            ->orWhere('phone', $validatedData['username'])->without('open_register')->first();

        if (!$user) {
            return response(['errors' => [
                'Login' => 'Invalid credentials',
            ]], 500);
        } else {
            if (!$user->verifyPwd($validatedData['password'])) {
                return response(['errors' => [
                    'Login' => 'Invalid credentials',
                ]], 500);
            }
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

            return response(['info' => ['Auth' => 'Password changed successfully!']]);
        } else {
            return response(['errors' => ['Auth' => 'Current password mismatch']], 422);
        }
    }

    public function verifySelfPwd(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string'
        ]);

        $user = auth()->user();

        if ($user->verifyPwd($validatedData['current_password'])) {
            return response(1, 200);
        } else {
            return response(['errors' => ['Verification' => 'Password verification failed']], 500);
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

        return response(['info' => ['Auth' => 'Password changed successfully!']]);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user->open_register) {
            $user->open_register->user_id = null;
            $user->open_register->save();
        }

        $user->token()->delete();

        return response(['info' => ['Logout' => 'Goodbye...']], 200);
    }


    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);


        $columns = ['username', 'name', 'email', 'phone'];
        $query = $this->model::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate(), 200);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);

        $user = User::create($validatedData);

        return response(['info' => ['User ' . $user->name . ' created successfully!']], 201);
    }

    public function update(User $model, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);

        $model->update($validatedData);
        $model->save();

        return response(['info' => ['User ' . $model->name . ' updated successfully!']], 200);
    }
}
