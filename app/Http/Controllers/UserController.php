<?php

namespace App\Http\Controllers;

use App\User;

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
}
