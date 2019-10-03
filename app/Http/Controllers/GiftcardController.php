<?php

namespace App\Http\Controllers;

use App\Giftcard;
use Illuminate\Http\Request;

class GiftcardController extends BaseController
{
    protected $model = Giftcard::class;
    
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|alpha',
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'amount' => 'required|numeric',
        ]);
        
        $giftcard = $this->model::store($validatedData);
        
        return response($giftcard, 201);
    }
}
