@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="relative flex items-center px-5 py-5 bg-white shadow-lg">
        <a href="/"
            class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white/10 backdrop-blur-sm z-10">
            <img src="{{ asset('assets/images/icons/tabler_arrow-left.svg') }}" class="w-8 h-8" alt="icon">
        </a>
        <h1 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-2xl font-bold m-0">
            Order
        </h1>
    </div>
    @if (count($cart))
        <ul class="px-5 pt-4 pb-2 pb-[160px] flex flex-col gap-4">
            @php $total = 0; @endphp
            @foreach ($cart as $index => $item)
                @php
                    $menu = $item['menu'];
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
                    $itemTotal = ($menu->price + $toppingTotal) * $item['quantity'];
                    $total += $itemTotal;
                @endphp
                <li class="flex gap-3 bg-white rounded-2xl shadow p-3 relative">
                    @if ($menu && $menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                            class="w-20 h-20 object-cover rounded-xl border border-gray-100">
                    @endif
                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-lg">{{ $menu->name ?? 'Menu tidak ditemukan' }}</span>
                                <span
                                    class="inline-block bg-ngekos-orange text-white text-xs font-bold px-2 py-1 rounded-full">
                                    x{{ $item['quantity'] }}
                                </span>
                            </div>
                            <div class="text-base text-gray-500">
                                Harga Satuan: <span class="font-semibold text-gray-700">Rp
                                    {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-base text-gray-500">
                                Topping:
                                @if (!empty($toppingNames))
                                    <span class="inline-block bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded ml-1">
                                        {{ implode(', ', $toppingNames) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div class="text-base text-gray-500">
                                Catatan: <span class="text-gray-700">{{ $item['notes'] ?? '-' }}</span>
                            </div>
                            @if (!empty($item['spiciness_level']))
                                <div class="text-base text-gray-500">
                                    Level Pedas:
                                    <span class="inline-block bg-red-100 text-red-700 text-xs px-2 py-1 rounded ml-1">
                                        @switch($item['spiciness_level'])
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
                                        @endswitch
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-2 py-1">
                                <button type="button"
                                    class="cursor-pointer qty-btn w-8 h-8 flex items-center justify-center bg-ngekos-orange/90 hover:bg-ngekos-orange rounded-lg transition"
                                    data-index="{{ $index }}" data-action="decrease">
                                    <img src="{{ asset('assets/images/icons/minus.svg') }}" alt="minus" class="w-5 h-5">
                                </button>
                                <span class="mx-2 qty-value font-semibold text-base"
                                    id="qty-{{ $index }}">{{ $item['quantity'] }}</span>
                                <button type="button"
                                    class="cursor-pointer qty-btn w-8 h-8 flex items-center justify-center bg-ngekos-orange/90 hover:bg-ngekos-orange rounded-lg transition"
                                    data-index="{{ $index }}" data-action="increase">
                                    <img src="{{ asset('assets/images/icons/plus.svg') }}" alt="plus" class="w-5 h-5">
                                </button>
                            </div>
                            <div class="text-xs font-semibold text-right">
                                Subtotal:<br>
                                <span id="subtotal-{{ $index }}" class="text-lg text-ngekos-orange font-bold">Rp
                                    {{ number_format($itemTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <form action="{{ route('cart.remove', $index) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="cursor-pointer text-base text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="h-[160px]"></div>
        <div
            class="fixed bottom-0 left-0 right-0 w-full max-w-[640px] mx-auto z-20 px-5 pb-5 bg-gradient-to-t from-white via-white/80 to-transparent">
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <h2 class="font-bold text-lg mb-2">Detail Pembayaran</h2>
                <div class="flex justify-between items-center mb-2">
                    <span>Total Harga :</span>
                    <span class="font-bold text-ngekos-orange text-xl" id="total-payment">Rp
                        {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('payment') }}"
                    class="block w-full h-[50px] mt-3 rounded-xl bg-[#811D0E] text-white font-bold text-lg flex items-center justify-center shadow transition hover:bg-orange-700">
                    Lanjut Pembayaran
                </a>
            </div>
        </div>
    @else
        <p class="text-center py-10 text-gray-400">Keranjang kosong.</p>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.qty-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const index = this.dataset.index;
                    const action = this.dataset.action;
                    fetch(`/cart/update/${index}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                action
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('qty-' + index).textContent = data
                                    .quantity;
                                document.getElementById('subtotal-' + index).textContent =
                                    'Rp ' + data.subtotal;
                                document.getElementById('total-payment').textContent = 'Rp ' +
                                    data.total;
                            }
                        });
                });
            });
        });
    </script>
@endsection
