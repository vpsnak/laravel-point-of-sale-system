<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailReceiptController extends Controller
{
    public function template()
    {
        return view('send_email');
    }

    public function send(Order $order, Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email'
        ]);

        Mail::to($validatedData['email'])->send(new SendMail($order));

        if (!Mail::failures()) {
            return response(['notification' => [
                'msg' => ['Receipt sent successfully!'],
                'type' => 'success'
            ]]);
        } else {
            return response(['errors' => Mail::failures()]);
        }
    }
}
