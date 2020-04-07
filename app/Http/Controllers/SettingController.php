<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private $data;

    public function setThemeDark(Request $request)
    {
        $this->data = $request->validate([
            'value' => 'required|boolean'
        ]);
        $userThemeDark = Setting::getUserThemeDark(auth()->user()->id);
        $userThemeDark->update($this->data);

        return response($userThemeDark);
    }
}
