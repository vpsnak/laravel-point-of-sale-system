<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GuestEmailList;

class GuestEmailListController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email'
        ]);

        if (!GuestEmailList::whereEmail($validatedData['email'])->first()) {
            $guestEmail = new GuestEmailList($validatedData);
            $guestEmail->save();

            return response('Email stored successfully', 201);
        } else {
            return response('Email already exists');
        }
    }
}
