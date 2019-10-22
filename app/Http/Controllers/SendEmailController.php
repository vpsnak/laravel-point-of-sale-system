<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Order;

class SendEmailController extends Controller
{
    function index()
    {
     return view('send_email');
    }

    function send(Order $order)
    {
        if (Mail::to('npapaioannou@webo2.gr')->send(new SendMail($order))) {
            return response(['type' => 'success', 'msg' => 'Email sent successfully']);
        }
        return response(['type' => 'error', 'msg' => 'There was an error sending the e-mail<br>Please try again']);
    }
}