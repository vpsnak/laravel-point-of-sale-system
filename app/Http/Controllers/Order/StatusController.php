<?php

namespace App\Http\Controllers;

use App\Status;

class StatusController extends Controller
{
    public function all()
    {
        return response(Status::all());
    }
}
