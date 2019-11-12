<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;

class CountryController extends BaseController
{
    protected $model = Country::class;

    public function all()
    {
        return response(Country::all());
    }
}
