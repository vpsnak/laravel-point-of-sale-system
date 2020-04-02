<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function all()
    {
        return response(BankAccount::paginate(10));
    }
}
