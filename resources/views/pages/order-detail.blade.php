{{-- resources/views/pages/order-detail.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
    <div
        class="w-full max-w-lg mx-auto mt-10 bg-white rounded-xl shadow-lg p-7 border border-dashed border-gray-300 relative font-mono">
        <div class="text-center mb-4 mt-2">
            <div class="text-2xl font-bold text-[#811D0E] tracking-widest">STRUK PESANAN</div>
            <div class="text-base text-gray-400">Terima kasih telah berbelanja!</div>
        </div>
        <div class="border-t border-dashed border-gray-300 my-3"></div>
        <div class="text-sm space-y-3">
            <div class="flex justify-between">
                <span>Order ID</span>
                <span>{{ $order->order_id }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nama</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>No HP</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            <div class="flex justify-between">
                <span>Email</span>
                <span>{{ $order->customer_email }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nomor Meja</span>
                <span>{{ $order->table_number }}</span>
            </div>
        </div>
        <div class="border-t border-dashed border-gray-300 my-3"></div>
        <div class="text-sm mb-2 font-bold text-gray-700">Daftar Makanan:</div>
        <ul class="text-sm mb-2">
            @forelse ($order->items as $item)
                <li class="py-2">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $item->menu->name ?? '-' }}</span>
                        <span>x{{ $item->quantity }}</span>
                    </div>
                    @if ($item->toppings && is_string($item->toppings) && json_decode($item->toppings))
                        <div class="text-xs text-gray-500 ml-4 mt-1">
                            @foreach (json_decode($item->toppings) as $topping)
                                <div>+ {{ $topping->name }} (Rp {{ number_format($topping->price, 0, ',', '.') }})</div>
                            @endforeach
                        </div>
                    @endif
                    @if ($item->spiciness_level)
                        <div class="text-xs text-gray-500 ml-4 mt-1">
                            Level Pedas: 
                            @switch($item->spiciness_level)
                                @case('original')
                                    Original (Tidak Pedas)
                                    @break
                                @case('mild')
                                    Sedikit Pedas
                                    @break
                                @case('medium')
                                    Pedas Sedang
                                    @break
                                @case('extra_pedas')
                                    Extra Pedas
                                    @break
                                @default
                                    {{ $item->spiciness_level }}
                            @endswitch
                        </div>
                    @endif
                    @if ($item->note)
                        <div class="text-xs text-gray-500 ml-4 mt-1">
                            Catatan: {{ $item->note }}
                        </div>
                    @endif
                </li>
            @empty
                <li class="text-gray-400 italic">Tidak ada item.</li>
            @endforelse
        </ul>
        <div class="border-t border-dashed border-gray-300 my-3"></div>
        <div class="flex justify-between text-lg font-bold">
            <span>Total</span>
            <span class="text-[#811D0E]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-sm mt-2">
            <span>Status</span>
            <span>
                @if ($order->status == 'success' || $order->status == 'paid')
                    <span class="text-green-600 font-bold">Sudah Dibayar</span>
                @elseif($order->status == 'pending')
                    <span class="text-yellow-600 font-bold">Menunggu</span>
                @elseif($order->status == 'challenge')
                    <span class="text-orange-600 font-bold">Verifikasi</span>
                @elseif($order->status == 'expired')
                    <span class="text-gray-600 font-bold">Kadaluarsa</span>
                @else
                    <span class="text-red-600 font-bold">Gagal</span>
                @endif
            </span>
        </div>
        <div class="border-t border-dashed border-gray-300 my-3"></div>
        <div class="text-center text-xs text-gray-400 mt-2 mb-6">* Simpan struk ini sebagai bukti pembayaran</div>
        <!-- Tombol Kembali di bawah struk -->
        <div class="flex justify-center">
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-2 bg-[#811D0E] hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded-full shadow transition text-base">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection