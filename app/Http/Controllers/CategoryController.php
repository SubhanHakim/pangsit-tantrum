<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $CategoryCount = Category::count();

        return view('pages.home', compact('categories', 'CategoryCount'));
    }
}
