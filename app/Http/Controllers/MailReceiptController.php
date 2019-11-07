<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Order;

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
            return response(['notification' => ['type' => 'success', 'msg' => 'Email sent successfully']]);
        } else {
            return response(['notification' => ['type' => 'error', 'msg' => 'There was an error sending the e-mail<br>Please try again']]);
        }
    }
}
