<?php
// app/Http/Controllers/KitchenController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function dashboard()
    {
        $pendingOrders = Order::with(['items.menu'])
            ->where('food_status', 'pending')
            ->whereIn('status', ['paid', 'success'])
            ->orderBy('created_at', 'asc')
            ->get();

        $preparingOrders = Order::with(['items.menu'])
            ->where('food_status', 'preparing')
            ->whereIn('status', ['paid', 'success'])
            ->orderBy('preparing_at', 'asc')
            ->get();

        $readyOrders = Order::with(['items.menu'])
            ->where('food_status', 'ready')
            ->whereIn('status', ['paid', 'success'])
            ->orderBy('ready_at', 'asc')
            ->get();

        return view('kitchen.dashboard', compact('pendingOrders', 'preparingOrders', 'readyOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'food_status' => 'required|in:pending,preparing,ready,completed'
        ]);

        $status = $request->food_status;
        $now = now();

        $order->food_status = $status;

        switch ($status) {
            case 'preparing':
                $order->preparing_at = $now;
                break;
            case 'ready':
                $order->ready_at = $now;
                break;
            case 'completed':
                $order->completed_at = $now;
                break;
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'order' => $order
        ]);
    }
}