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

            switch ($transactionStatus) {
                case 'capture':
                    if ($paymentType == 'credit_card') {
                        if ($fraudStatus == 'challenge') {
                            $transaction->update(['status' => 'challenge']);
                        } else {
                            $transaction->update(['status' => 'success']);
                            // $this->reduceStock($transaction); 
                        }
                    }
                    break;

                case 'settlement':
                    $transaction->update(['status' => 'success']);
                    // $this->reduceStock($transaction); 
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
