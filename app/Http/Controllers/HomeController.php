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
        $menuMakanans = Menu::where('category_id', 1)->latest()->take(4)->get();
        $menuMinumans = Menu::where('category_id', 2)->latest()->take(4)->get();

        return view('pages.home', compact('categories', 'CategoryCount', 'menus', 'menuMakanans', 'menuMinumans'));
    }
}
