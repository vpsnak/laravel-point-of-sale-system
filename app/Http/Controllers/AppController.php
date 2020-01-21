<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function env()
    {
        return response(config('app.env'));
    }

    public function debug()
    {
        return response(config('app.debug'));
    }

    public function receipt()
    {
        $receipt = null;

        return view('receipt')->with('receipt', $receipt);
    }
}
