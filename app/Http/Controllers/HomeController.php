<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $CategoryCount = Category::count();
        $menus = Menu::latest()->take(4)->get();
        $menuMakanans = Category::where('name', 'Makanan')->first()?->menus()->latest()->take(4)->get() ?? collect();
        $menuMinumans = Category::where('name', 'Minuman')->first()?->menus()->latest()->take(4)->get() ?? collect();

        return view('pages.home', compact('categories', 'CategoryCount', 'menus', 'menuMakanans', 'menuMinumans'));
    }
}
