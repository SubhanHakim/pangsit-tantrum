<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;

class MidtransController extends Controller
{

    public function callback(Request $request)
    {
        $notif = json_decode($request->getContent());
        $order = Order::where('order_id', $notif->order_id)->first();

        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        // transaction_status dari Midtrans, bukan dari database
        if ($notif->transaction_status == 'settlement' || $notif->transaction_status == 'capture') {
            $order->status = 'paid';
        } elseif ($notif->transaction_status == 'pending') {
            $order->status = 'pending';
        } elseif ($notif->transaction_status == 'expire' || $notif->transaction_status == 'cancel') {
            $order->status = 'failed';
        }
        $status = Order::status($notif->order_id);

        $order->save();
        FacadesLog::info('Order status updated:', [
            'order_id' => $order->order_id,
            'status' => $order->status,
        ]);
        FacadesLog::info('Transaction Status:', (array)$status);
        FacadesLog::info('Midtrans Callback Received:', (array)json_decode($request->getContent(), true));
        return response()->json(['message' => 'Notification handled']);
    }
}
