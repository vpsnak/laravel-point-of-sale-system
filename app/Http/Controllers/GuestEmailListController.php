<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GuestEmailList;

class GuestEmailListController extends Controller
{
    public function all()
    {
        return response(GuestEmailList::all());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:guest_email_lists,email|unique:customers,email'
        ]);

        (new GuestEmailList($validatedData))->save();

        return response('Email stored successfully', 201);
    }
}
