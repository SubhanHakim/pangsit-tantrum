{{-- resources/views/pages/order-detail.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
    <div class="max-w-md mx-auto mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Detail Pesanan</h2>
        <div class="mb-2"><b>Order ID:</b> {{ $order->order_id }}</div>
        <div class="mb-2"><b>Nama:</b> {{ $order->customer_name }}</div>
        <div class="mb-2"><b>No HP:</b> {{ $order->customer_phone }}</div>
        <div class="mb-2"><b>Email:</b> {{ $order->customer_email }}</div>
        <div class="mb-2"><b>Nomor Meja:</b> {{ $order->table_number }}</div>
        <div class="mb-2"><b>Total:</b> Rp {{ number_format($order->total, 0, ',', '.') }}</div>
        <div class="mb-2"><b>Status:</b>
            @if ($order->status == 'paid')
                <span class="text-green-600 font-bold">Sudah Dibayar</span>
            @elseif($order->status == 'pending')
                <span class="text-yellow-600 font-bold">Menunggu Pembayaran</span>
            @else
                <span class="text-red-600 font-bold">Gagal</span>
            @endif
        </div>
        <div class="mt-4">
            <b>Daftar Makanan:</b>
            <ul>
                @foreach ($order->items as $item)
                    <li>{{ $item->menu->name ?? '-' }} x{{ $item->quantity }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
