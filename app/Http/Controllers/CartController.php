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

    public function payment(Request $request)
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

        $selectedTable = null;
        if ($request->has('table_code')) {
            $tableCode = $request->input('table_code');
            $selectedTable = Table::where('qr_code', $tableCode)->where('is_active', true)->first();
        }

        return view('pages.payment', compact('cart', 'total', 'tables', 'selectedTable'));
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
        $itemDetails = []; // Untuk menyimpan detail item beserta toppingnya

        foreach ($cart as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            $toppingTotal = 0;
            $toppingDetails = [];

            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $toppingId) {
                    $topping = $menu->toppings->where('id', $toppingId)->first();
                    if ($topping) {
                        $toppingTotal += $topping->price;
                        $toppingDetails[] = [
                            'id' => $topping->id,
                            'name' => $topping->name,
                            'price' => $topping->price
                        ];
                    }
                }
            }

            $itemPrice = $menu->price + $toppingTotal;
            $subtotal = $itemPrice * $item['quantity'];
            $total += $subtotal;

            $itemDetails[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'menu_price' => $menu->price,
                'quantity' => $item['quantity'],
                'toppings' => $toppingDetails,
                'note' => $item['notes'] ?? null,
                'subtotal' => $subtotal
            ];
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

        // Simpan order items dan toppings
        foreach ($itemDetails as $detail) {
            // Buat order item
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $detail['menu_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['menu_price'],
                'note' => $detail['note']
            ]);

            // Attach toppings ke order item (many-to-many)
            if (!empty($detail['toppings'])) {
                $orderItem->update(['toppings' => json_encode($detail['toppings'])]);
            }
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

        // Hapus cart setelah order berhasil dibuat
        session()->forget('cart');

        $snapToken = Snap::getSnapToken($params);

        // Kirim ke halaman Snap
        return view('pages.midtrans', compact('snapToken', 'total', 'orderId'));
    }
}
