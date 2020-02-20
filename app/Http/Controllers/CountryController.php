<?php

namespace App\Http\Controllers;

use App\Country;

class CountryController extends Controller
{
    public function all()
    {
        return response(Country::with('regions')->get());
    }

    public function regions(Country $model)
    {
        return response($model->regions);
    }
}
