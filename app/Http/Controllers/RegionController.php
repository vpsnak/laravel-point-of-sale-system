<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

class RegionController extends BaseController
{
    protected $model = Region::class;

    public function all()
    {
        return response(Region::all());
    }
}
