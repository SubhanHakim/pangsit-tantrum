<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Topping;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function show($id)
    {
        $menu = Menu::with('toppings')->findOrFail($id);
        return view('pages.menu-detail', compact('menu'));
    }
}
