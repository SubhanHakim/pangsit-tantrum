<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $paymentType       = $notification->payment_type;
            $orderId           = $notification->order_id;
            $fraudStatus       = $notification->fraud_status ?? null;

            Log::info("Midtrans callback received", [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'fraud_status' => $fraudStatus,
            ]);

            $transaction = Order::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning("Transaction not found for order_id: " . $orderId);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $sendWa = false;

            switch ($transactionStatus) {
                case 'capture':
                    if ($paymentType == 'credit_card') {
                        if ($fraudStatus == 'challenge') {
                            $transaction->update(['status' => 'challenge']);
                        } else {
                            $transaction->update(['status' => 'success']);
                            $sendWa = true;
                        }
                    }
                    break;

                case 'settlement':
                    $transaction->update(['status' => 'success']);
                    $sendWa = true;
                    break;

                case 'pending':
                    $transaction->update(['status' => 'pending']);
                    break;

                case 'deny':
                    $transaction->update(['status' => 'failed']);
                    break;

                case 'expire':
                    $transaction->update(['status' => 'expired']);
                    break;

                case 'cancel':
                    $transaction->update(['status' => 'failed']);
                    break;

                default:
                    $transaction->update(['status' => 'unknown']);
                    break;
            }

            // Kirim WhatsApp via Fonnte hanya jika pembayaran sukses
            if ($sendWa) {
                try {
                    $waNumber = $transaction->customer_phone; // pastikan format 62xxx
                    if (substr($waNumber, 0, 1) === '0') {
                        $waNumber = '62' . substr($waNumber, 1);
                    }
                    $orderLink = url('/order/' . $transaction->order_id);
                    $message = "Terima kasih, pesanan Anda telah dibayar!\n\n"
                        . "Order ID: {$transaction->order_id}\n"
                        . "Nama: {$transaction->customer_name}\n"
                        . "Total: Rp " . number_format($transaction->total, 0, ',', '.') . "\n"
                        . "Detail pesanan: {$orderLink}";

                    $client = new \GuzzleHttp\Client();
                    $client->request('POST', 'https://api.fonnte.com/send', [
                        'headers' => [
                            'Authorization' => 'XnDDvQvwPkguiJNamsTq',
                        ],
                        'form_params' => [
                            'target' => $waNumber,
                            'message' => $message,
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::error('Fonnte WA Error: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction updated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Midtrans Callback Error", [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Callback error: ' . $e->getMessage()
            ], 500);
        }
    }
}
