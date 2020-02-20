<?php

namespace App\Http\Controllers;

use App\Region;

class RegionController extends Controller
{
    public function all()
    {
        return response(Region::all());
    }
}
