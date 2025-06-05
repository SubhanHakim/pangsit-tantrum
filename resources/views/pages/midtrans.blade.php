{{-- filepath: resources/views/pages/midtrans.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50">
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-8 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4 text-center">Pembayaran</h2>
            <div class="mb-4 text-center">
                <span class="font-semibold">Total:</span>
                <span class="font-bold text-ngekos-orange text-xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button id="pay-button"
                class="w-full h-[50px] mt-3 rounded-xl bg-ngekos-orange text-white font-bold text-lg flex items-center justify-center shadow transition hover:bg-orange-700">
                Bayar Sekarang
            </button>
        </div>
    </div>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    // Redirect ke halaman detail pesanan, misal: /order/{order_id}
                    window.location.href = '/order/{{ $orderId }}?status=success';
                },
                onPending: function(result) {
                    window.location.href = '/order/{{ $orderId }}?status=pending';
                },
                onError: function(result) {
                    window.location.href = '/order/{{ $orderId }}?status=failed';
                }
            });
        }
    </script>
@endsection
