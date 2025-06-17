{{-- filepath: resources/views/pages/midtrans.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-[#fff7f4] to-[#fbeee7] py-8">
        <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md border border-orange-100 relative">
            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#fff7f4] rounded-full shadow-lg p-3 border border-orange-100">
                <img src="{{ asset('assets/images/icons/tabler_wallet.svg') }}" class="w-12 h-12" alt="wallet">
            </div>
            <h2 class="text-2xl font-extrabold mb-6 text-center text-[#811D0E] tracking-wide mt-8">
                Konfirmasi Pembayaran
            </h2>
            <div class="mb-6 text-center">
                <span class="font-semibold text-gray-700">Total Tagihan</span>
                <div class="font-extrabold text-[#811D0E] text-3xl mt-1 drop-shadow">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </div>
            </div>
            <button id="pay-button"
                class="w-full h-[50px] mt-3 rounded-xl bg-[#811D0E] text-white font-bold text-lg flex items-center justify-center shadow transition hover:bg-orange-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-orange-300">
                <img src="{{ asset('assets/images/icons/tabler_credit-card.svg') }}" class="w-6 h-6 mr-2" alt="icon">
                Bayar Sekarang
            </button>
            <div class="mt-8 text-center text-gray-400 text-xs">
                Setelah pembayaran berhasil, Anda akan diarahkan ke halaman detail pesanan.
            </div>
        </div>
    </div>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
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