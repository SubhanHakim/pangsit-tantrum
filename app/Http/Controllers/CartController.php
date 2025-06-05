<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $cart = session()->get('cart', []);
        $cart[] = [
            'menu_id' => $request->menu_id,
            'toppings' => $request->toppings ?? [],
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ];
        session(['cart' => $cart]);
        return response()->json(['success' => true, 'message' => 'Menu berhasil ditambahkan ke keranjang!']);
    }

    public function checkout()
    {
        $cart = session('cart', []);
        // Ambil data menu untuk setiap item di cart
        $cartWithMenu = collect($cart)->map(function ($item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            $item['menu'] = $menu;
            return $item;
        });
        return view('pages.checkout', ['cart' => $cartWithMenu]);
    }


    public function remove($index)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            // Re-index array agar tidak ada index yang hilang
            $cart = array_values($cart);
            session(['cart' => $cart]);
        }
        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function update(Request $request, $index)
    {
        $cart = session('cart', []);
        if (isset($cart[$index])) {
            if ($request->action === 'increase') {
                $cart[$index]['quantity']++;
            } elseif ($request->action === 'decrease' && $cart[$index]['quantity'] > 1) {
                $cart[$index]['quantity']--;
            }
            session(['cart' => $cart]);
            // Hitung ulang subtotal & total
            $item = $cart[$index];
            $menu = \App\Models\Menu::find($item['menu_id']);
            $toppingTotal = 0;
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $toppingId) {
                    $topping = $menu->toppings->where('id', $toppingId)->first();
                    if ($topping) $toppingTotal += $topping->price;
                }
            }
            $subtotal = number_format(($menu->price + $toppingTotal) * $item['quantity'], 0, ',', '.');
            $total = 0;
            foreach ($cart as $c) {
                $m = \App\Models\Menu::find($c['menu_id']);
                $t = 0;
                if (!empty($c['toppings'])) {
                    foreach ($c['toppings'] as $tid) {
                        $tp = $m->toppings->where('id', $tid)->first();
                        if ($tp) $t += $tp->price;
                    }
                }
                $total += ($m->price + $t) * $c['quantity'];
            }
            return response()->json([
                'success' => true,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
                'total' => number_format($total, 0, ',', '.')
            ]);
        }
        return response()->json(['success' => false]);
    }

public function payment()
{
    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('checkout')->with('error', 'Keranjang belanja kosong.');
    }

    $total = 0;
    foreach ($cart as $item) {
        $menu = \App\Models\Menu::find($item['menu_id']);
        $toppingTotal = 0;
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $toppingId) {
                $topping = $menu->toppings->where('id', $toppingId)->first();
                if ($topping) $toppingTotal += $topping->price;
            }
        }
        $total += ($menu->price + $toppingTotal) * $item['quantity'];
    }
    $tables = Table::all();
    return view('pages.payment', compact('cart', 'total', 'tables'));
}

public function processPayment(Request $request)
{
    $request->validate([
        'customer_name' => 'required',
        'customer_phone' => 'required',
        'customer_email' => 'required|email',
        'table_number' => 'required',
    ]);

    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('checkout')->with('error', 'Keranjang belanja kosong.');
    }

    $total = 0;
    foreach ($cart as $item) {
        $menu = \App\Models\Menu::find($item['menu_id']);
        $toppingTotal = 0;
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $toppingId) {
                $topping = $menu->toppings->where('id', $toppingId)->first();
                if ($topping) $toppingTotal += $topping->price;
            }
        }
        $total += ($menu->price + $toppingTotal) * $item['quantity'];
    }

    // Konfigurasi Midtrans
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = config('midtrans.is_sanitized');
    Config::$is3ds = config('midtrans.is_3ds');

    $orderId = 'ORDER-' . uniqid();

    // Simpan order ke database
    $order = Order::create([
        'order_id' => $orderId,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'customer_email' => $request->customer_email,
        'table_number' => $request->table_number,
        'total' => $total,
        'status' => 'pending',
    ]);

    foreach ($cart as $item) {
        OrderItem::create([
            'order_id' => $order->id, // pastikan order_id di order_items adalah FK ke orders.id
            'menu_id' => $item['menu_id'],
            'quantity' => $item['quantity'],
            'price' => $menu->price,
            'note' => $item['note'] ?? null,
        ]);
    }

    $customer = [
        'first_name' => $request->customer_name,
        'email' => $request->customer_email,
        'phone' => $request->customer_phone,
    ];

    $params = [
        'transaction_details' => [
            'order_id' => $orderId,
            'gross_amount' => $total,
        ],
        'customer_details' => $customer,
    ];

    $snapToken = Snap::getSnapToken($params);

    // Kirim ke halaman Snap
    return view('pages.midtrans', compact('snapToken', 'total', 'orderId'));
}
}
