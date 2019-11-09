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
                'info' => ['Login' => '<h2>Welcome ' . $user->name . '</h2>'],
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }
    
    
    public function postCredentials(Request $request)
        {
            if(Auth::Check())
            {
                $request_data = $request->All();
                $validator = $this->admin_credential_rules($request_data);
                if($validator->fails())
                {
                return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
                }
                else
                {  
                $current_password = Auth::User()->password;           
                if(Hash::check($request_data['current_password'], $current_password))
                {           
                    $user_id = Auth::User()->id;                       
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['password']);;
                    $obj_user->save(); 
                    return "ok";
                }
                else
                {           
                    $error = array('current_password' => 'Please enter correct current password');
                    return response()->json(array('error' => $error), 400);   
                }
                }        
            }
            else
            {
                return redirect()->to('/');
            }    
        }
        
        
        public function admin_credential_rules(array $data)
        {
            $messages = [
                'current_password.required' => 'Please enter current password',
                'password.required' => 'Please enter password',
            ];

            $validator = Validator::make($data, [
                'current_password' => 'required',
                'password' => 'required|same:password',
                'password_confirmation' => 'required|same:password',     
            ], $messages);

            return $validator;
            }  

                public function logout()
                {
                    auth()->user()->token()->revoke();

                    return response(['info' => ['Logout' => '<h2>Goodbye...</h2>']], 200);
                }
        }
