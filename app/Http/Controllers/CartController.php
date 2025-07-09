<?php
// filepath: d:\project\client\pangsit-tantrum\app\Http\Controllers\CartController.php

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
        $menu = \App\Models\Menu::find($request->menu_id);
        
        $cart[] = [
            'menu_id' => $request->menu_id,
            'toppings' => $request->toppings ?? [],
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'spiciness_level' => $menu && $menu->has_spiciness_option ?
                ($request->spiciness_level ?? 'original') : null,
        ];
        
        session(['cart' => $cart]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Menu berhasil ditambahkan ke keranjang!'
        ]);
    }

    public function checkout()
    {
        $cart = session('cart', []);
        
        // Ambil data menu untuk setiap item di cart
        $cartWithMenu = collect($cart)->map(function ($item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            $item['menu'] = $menu;
            
            // Hitung total termasuk toppings
            $toppingTotal = 0;
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $toppingId) {
                    $topping = $menu->toppings->where('id', $toppingId)->first();
                    if ($topping) {
                        $toppingTotal += $topping->price;
                    }
                }
            }
            
            $item['item_total'] = ($menu->price + $toppingTotal) * $item['quantity'];
            $item['topping_total'] = $toppingTotal;
            
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
            
            // Hitung total keseluruhan
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

        // Ambil semua table untuk dropdown
        $tables = Table::orderBy('name')->get();

        $selectedTable = null;
        if ($request->has('table_code')) {
            $tableCode = $request->input('table_code');
            $selectedTable = Table::where('qr_code', $tableCode)->first();
        }

        return view('pages.payment', compact('cart', 'total', 'tables', 'selectedTable'));
    }

    public function processPayment(Request $request)
    {
        // Validasi dinamis berdasarkan order_type
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'order_type' => 'required|in:dine_in,takeaway',
            'table_number' => 'required_if:order_type,dine_in|nullable|exists:tables,id',
        ], [
            'customer_name.required' => 'Nama harus diisi',
            'customer_phone.required' => 'Nomor telepon harus diisi',
            'customer_email.required' => 'Email harus diisi',
            'customer_email.email' => 'Format email tidak valid',
            'order_type.required' => 'Pilih tipe pesanan',
            'order_type.in' => 'Tipe pesanan tidak valid',
            'table_number.required_if' => 'Nomor meja harus dipilih untuk makan di tempat',
            'table_number.exists' => 'Nomor meja tidak valid',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('checkout')->with('error', 'Keranjang belanja kosong.');
        }

        $total = 0;
        $itemDetails = [];

        foreach ($cart as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            if (!$menu) {
                continue; // Skip jika menu tidak ditemukan
            }

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
                'spiciness_level' => $item['spiciness_level'] ?? null,
                'subtotal' => $subtotal
            ];
        }

        if (empty($itemDetails)) {
            return redirect()->route('checkout')->with('error', 'Tidak ada item yang valid di keranjang.');
        }

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'ORDER-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);

        try {
            // Simpan order ke database
            $order = Order::create([
                'order_id' => $orderId,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'table_number' => $request->order_type === 'dine_in' ? $request->table_number : null,
                'order_type' => $request->order_type,
                'total' => $total,
                'status' => 'pending',
                'food_status' => 'pending',
            ]);

            // Simpan order items
            foreach ($itemDetails as $detail) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $detail['menu_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['menu_price'],
                    'note' => $detail['note'],
                    'spiciness_level' => $detail['spiciness_level'],
                ]);

                // Simpan toppings sebagai JSON
                if (!empty($detail['toppings'])) {
                    $orderItem->update(['toppings' => json_encode($detail['toppings'])]);
                }
            }

            $customer = [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ];

            // Tambahkan item details untuk Midtrans
            $midtransItems = [];
            foreach ($itemDetails as $detail) {
                $midtransItems[] = [
                    'id' => $detail['menu_id'],
                    'price' => $detail['menu_price'],
                    'quantity' => $detail['quantity'],
                    'name' => $detail['menu_name'],
                ];

                // Tambahkan toppings sebagai item terpisah
                if (!empty($detail['toppings'])) {
                    foreach ($detail['toppings'] as $topping) {
                        $midtransItems[] = [
                            'id' => 'topping_' . $topping['id'],
                            'price' => $topping['price'],
                            'quantity' => $detail['quantity'],
                            'name' => $topping['name'] . ' (Topping)',
                        ];
                    }
                }
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => $customer,
                'item_details' => $midtransItems,
                'custom_field1' => $request->order_type,
                'custom_field2' => $request->table_number,
            ];

            $snapToken = Snap::getSnapToken($params);

            // Hapus cart setelah order berhasil dibuat
            session()->forget('cart');

            return view('pages.midtrans', compact('snapToken', 'total', 'orderId', 'order'));
            
        } catch (\Exception $e) {
            // Rollback jika ada error
            if (isset($order)) {
                $order->delete();
            }
            
            return redirect()->route('payment')
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function count()
    {
        $cart = session('cart', []);
        $totalItems = 0;
        
        foreach ($cart as $item) {
            $totalItems += $item['quantity'];
        }
        
        return response()->json(['count' => $totalItems]);
    }

    public function getCartTotal()
    {
        $cart = session('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            if (!$menu) {
                continue; // Skip jika menu tidak ditemukan
            }

            $toppingTotal = 0;
            
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $toppingId) {
                    $topping = $menu->toppings->where('id', $toppingId)->first();
                    if ($topping) $toppingTotal += $topping->price;
                }
            }
            
            $total += ($menu->price + $toppingTotal) * $item['quantity'];
        }
        
        return response()->json([
            'total' => $total,
            'formatted_total' => number_format($total, 0, ',', '.')
        ]);
    }

    public function validateCart()
    {
        $cart = session('cart', []);
        $validItems = [];
        $hasChanges = false;

        foreach ($cart as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            if ($menu) {
                $validItems[] = $item;
            } else {
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            session(['cart' => $validItems]);
        }

        return response()->json([
            'valid' => true,
            'changes' => $hasChanges,
            'count' => count($validItems)
        ]);
    }

    public function getCartItems()
    {
        $cart = session('cart', []);
        $cartItems = [];

        foreach ($cart as $index => $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            if ($menu) {
                $toppingTotal = 0;
                $toppingNames = [];

                if (!empty($item['toppings'])) {
                    foreach ($item['toppings'] as $toppingId) {
                        $topping = $menu->toppings->where('id', $toppingId)->first();
                        if ($topping) {
                            $toppingTotal += $topping->price;
                            $toppingNames[] = $topping->name;
                        }
                    }
                }

                $cartItems[] = [
                    'index' => $index,
                    'menu' => $menu,
                    'quantity' => $item['quantity'],
                    'toppings' => $toppingNames,
                    'topping_total' => $toppingTotal,
                    'item_total' => ($menu->price + $toppingTotal) * $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'spiciness_level' => $item['spiciness_level'] ?? null,
                ];
            }
        }

        return response()->json(['items' => $cartItems]);
    }
}