<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Xendit\Invoice\InvoiceApi;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\Invoice;

class XenditController extends Controller
{
    public function show($order_id)
    {
        $order = Order::where('order_id', $order_id)->with('items.menu')->firstOrFail();
        return view('pages.order-detail', compact('order'));
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
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'table_number' => 'required',
        ]);

        // Ambil cart dari session
        $cart = session('cart', []);
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
        $orderId = 'ORDER-' . time();

        // Simpan order ke database
        $order = Order::create([
            'order_id' => $orderId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'table_number' => $request->table_number,
            'total' => $total,
            'status' => 'pending',
        ]);

        // Simpan item pesanan ke order_items
        foreach ($cart as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
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
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $menu->price,
                'note' => !empty($toppingNames) ? implode(', ', $toppingNames) : null,
            ]);
        }

        // Inisialisasi Xendit
        Configuration::setXenditKey(config('xendit.secret_key'));

        // Buat invoice Xendit
        $params = [
            'external_id' => $orderId,
            'payer_email' => $request->customer_email,
            'description' => 'Pembayaran Pesanan #' . $orderId,
            'amount' => $total,
            'success_redirect_url' => url('/order/' . $orderId),
            'failure_redirect_url' => url('/order/' . $orderId . '?status=failed'),
        ];
        $invoiceApi = new InvoiceApi();
        $createInvoiceRequest = new CreateInvoiceRequest($params);

        try {
            $invoice = $invoiceApi->createInvoice($createInvoiceRequest);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }

        // Redirect ke halaman pembayaran Xendit
        return redirect($invoice['invoice_url']);
    }

    // Callback/Notification dari Xendit
    public function callback(Request $request)
    {
        $data = $request->all();
        $externalId = $data['external_id'] ?? null;

        $order = Order::where('order_id', $externalId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($data['status'] === 'SETTLED' || $data['status'] === 'PAID') {
            $order->status = 'paid';
        } elseif ($data['status'] === 'EXPIRED') {
            $order->status = 'expired';
        } elseif ($data['status'] === 'PENDING') {
            $order->status = 'pending';
        } else {
            $order->status = strtolower($data['status']);
        }
        $order->save();

        return response()->json(['message' => 'Callback handled']);
    }
}
