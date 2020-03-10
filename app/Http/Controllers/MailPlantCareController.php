<?php

namespace App\Http\Controllers;

use App\Mail\SendPlantCareMail;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailPlantCareController extends Controller
{
    public function send(Product $product, Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email'
        ]);

        Mail::to($validatedData['email'])->send(new SendPlantCareMail($product));

        if (!Mail::failures()) {
            return response(['notification' => [
                'msg' => ['Plant care sent successfully!'],
                'type' => 'success'
            ]]);
        } else {
            return response(['errors' => Mail::failures()]);
        }
    }
}
