<?php

namespace App\Http\Controllers;

class MenuItemController extends Controller
{
    public function all()
    {
        $role = auth()->user()->roles()->first();
        // @TODO reduce to one request
        $side_menu = $role->menuItems()->whereLocation('side_menu')->get();
        $top_menu = $role->menuItems()->whereLocation('top_menu')->get();

        return response([
            "side_menu" => $side_menu,
            "top_menu" => $top_menu
        ]);
    }
}
