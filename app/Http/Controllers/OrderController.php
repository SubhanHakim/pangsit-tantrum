<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function show($order_id)
    {
        $order = Order::where('order_id', $order_id)->with('items.menu')->firstOrFail();
        return view('pages.order-detail', compact('order'));
    }
}
