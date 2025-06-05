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
        <ul class="px-5 pt-4 pb-2 flex flex-col gap-4">
            @php $total = 0; @endphp
            @foreach ($cart as $index => $item)
                @php
                    $menu = $item['menu'];
                    $toppingTotal = 0;
                    if (!empty($item['toppings'])) {
                        foreach ($item['toppings'] as $toppingId) {
                            $topping = $menu->toppings->where('id', $toppingId)->first();
                            if ($topping) {
                                $toppingTotal += $topping->price;
                            }
                        }
                    }
                    $itemTotal = ($menu->price + $toppingTotal) * $item['quantity'];
                    $total += $itemTotal;
                @endphp
                <li class="flex gap-3 bg-white rounded-2xl shadow p-3">
                    @if ($menu && $menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                            class="w-16 h-16 object-cover rounded-xl border border-gray-100">
                    @endif
                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex flex-col gap-3">
                            <div class="font-bold text-lg mb-1">{{ $menu->name ?? 'Menu tidak ditemukan' }}</div>
                            <div class="text-base text-gray-500 mb-1">Harga: Rp
                                {{ number_format($menu->price, 0, ',', '.') }}</div>
                            <div class="text-base text-gray-500 mb-1">
                                Topping:
                                @if (!empty($item['toppings']))
                                    {{ implode(', ', $menu->toppings->whereIn('id', $item['toppings'])->pluck('name')->toArray()) }}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="text-base text-gray-500 mb-1">Catatan: {{ $item['notes'] ?? '-' }}</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
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
                                <span id="subtotal-{{ $index }}" class="text-base text-ngekos-orange">Rp
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
        <div
            class="fixed bottom-0 left-0 right-0 w-full max-w-[640px] mx-auto z-20 px-5 pb-5 bg-gradient-to-t from-white via-white/80 to-transparent">
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <h2 class="font-bold text-lg mb-2">Payment Details</h2>
                <div class="flex justify-between items-center mb-2">
                    <span>Total Payment:</span>
                    <span class="font-bold text-ngekos-orange text-xl" id="total-payment">Rp
                        {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('payment') }}"
                    class="block w-full h-[50px] mt-3 rounded-xl bg-[#811D0E] text-white font-bold text-lg flex items-center justify-center shadow transition hover:bg-orange-700">
                    Lanjut Pembayaran
                </a>
            </div>
        </div>
        <div class="h-[120px]"></div>
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
