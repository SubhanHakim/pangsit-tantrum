<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

    public function index(Request $request)
    {
        // Simpan table_code ke session jika ada
        if ($request->has('table_code')) {
            session(['table_code' => $request->table_code]);
        }

        // Ambil data kategori dan menu
        $categories = Category::with('menus')->get();

        // Ambil menu unggulan (featured)
        $featuredMenus = Menu::inRandomOrder()->take(4)->get();

        // Ambil menu berdasarkan kategori
        $menuMakanans = Menu::whereHas('category', function ($query) {
            $query->where('name', 'Makanan');
        })->latest()->take(4)->get();

        $menuMinumans = Menu::whereHas('category', function ($query) {
            $query->where('name', 'Minuman');
        })->latest()->take(4)->get();

        return view('pages.menu', compact(
            'categories',
            'featuredMenus',
            'menuMakanans',
            'menuMinumans'
        ));
    }

    public function detail($id)
    {
        $menu = Menu::with('category', 'toppings')->findOrFail($id);
        return view('pages.menu-detail', compact('menu'));
    }
}
