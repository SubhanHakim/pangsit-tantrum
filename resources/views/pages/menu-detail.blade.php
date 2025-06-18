@extends('layouts.app')

@section('title', $menu->name)

@section('content')
    <div id="ForegroundFade"
        class="absolute top-0 w-full h-[143px] bg-[linear-gradient(180deg,#070707_0%,rgba(7,7,7,0)_100%)] z-10">
    </div>
    <div id="TopNavAbsolute" class="absolute top-[60px] flex items-center justify-between w-full px-5 z-10">
        <a href="/"
            class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white/10 backdrop-blur-sm">
            <img src="{{ asset('assets/images/icons/arrow-left-transparent.svg') }}" class="w-8 h-8" alt="icon">
        </a>
        <p class="font-semibold text-white">Details</p>
        <button
            class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white/10 backdrop-blur-sm">
            <img src="{{ asset('assets/images/icons/like.svg') }}" class="w-[26px] h-[26px]" alt="">
        </button>
    </div>
    <div id="Gallery" class="swiper-gallery w-full overflow-x-hidden -mb-[38px]">
        <div class="swiper-wrapper">
            <div class="swiper-slide !w-full">
                <div class="flex shrink-0 w-full h-[430px] overflow-hidden relative">
                    <img src="{{ asset('storage/' . $menu->image) }}" class="absolute inset-0 w-full h-full object-cover"
                        alt="gallery thumbnails">
                </div>
            </div>
        </div>
    </div>
    <main id="Details" class="relative flex flex-col rounded-t-[40px] py-5 pb-[10px] gap-4 bg-white z-10">
        <div id="Title" class="flex flex-col justify-between gap-3 px-5">
            <h1 class="font-bold text-[24px] leading-[33px]">{{ $menu->name }}</h1>
            <p id="base-price" class="text-black" data-price="{{ $menu->price }}">
                Rp. {{ number_format($menu->price, 0, ',', '.') }}
            </p>
        </div>
        <hr class="border-[#F1F2F6] mx-5">
        <div id="Features" class="grid grid-cols-2 gap-x-[10px] gap-y-4 px-5">
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/images/icons/3dcube.svg') }}" class="w-[26px] h-[26px] flex shrink-0"
                    alt="icon">
                <p class="text-ngekos-grey">{{ $menu->category->name }}</p>
            </div>
        </div>
        <hr class="border-[#F1F2F6] mx-5">
        <form id="orderForm" class="relative flex flex-col gap-6 mt-5">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <div class="flex flex-col gap-[6px] px-5">
                <h1 class="font-semibold text-lg">Informasi order</h1>
            </div>
            <div id="InputContainer" class="flex flex-col gap-[18px]">
                @if ($menu->toppings->count())
                    <div class="flex flex-col w-full gap-2 px-5">
                        <p class="font-semibold">Pilih Topping</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($menu->toppings as $topping)
                                <label class="flex justify-between w-full items-center gap-4">
                                    <div class="flex items-center gap-3">
                                        <p class="text-lg">Extra {{ $topping->name }}</p>
                                        <p class="text-black text-lg">( Rp.
                                            {{ number_format($topping->price, 0, ',', '.') }} )</p>
                                    </div>
                                    <input type="checkbox" name="toppings[]" value="{{ $topping->id }}"
                                        class="topping-checkbox peer appearance-none w-5 h-5 border border-[#811D0E] rounded-md checked:bg-[#811D0E] checked:border-[#811D0E] focus:ring-2 focus:ring-[#811D0E] transition"
                                        data-price="{{ $topping->price }}">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="mt-4">
                    <p class="font-semibold text-lg">Pilih Level Pedas:</p>
                    <div class="flex flex-col gap-2 mt-2">
                        <label class="flex items-center gap-3">
                            <input type="radio" name="spiciness_level" value="original"
                                {{ $menu->spiciness_level == 'original' ? 'checked' : '' }}
                                class="form-radio h-5 w-5 text-[#811D0E]">
                            <span class="text-lg">Original (Tidak Pedas)</span>
                        </label>

                        <label class="flex items-center gap-3">
                            <input type="radio" name="spiciness_level" value="mild"
                                {{ $menu->spiciness_level == 'mild' ? 'checked' : '' }}
                                class="form-radio h-5 w-5 text-[#811D0E]">
                            <span class="text-lg">Sedikit Pedas</span>
                        </label>

                        <label class="flex items-center gap-3">
                            <input type="radio" name="spiciness_level" value="medium"
                                {{ $menu->spiciness_level == 'medium' ? 'checked' : '' }}
                                class="form-radio h-5 w-5 text-[#811D0E]">
                            <span class="text-lg">Pedas Sedang</span>
                        </label>

                        <label class="flex items-center gap-3">
                            <input type="radio" name="spiciness_level" value="extra_pedas"
                                {{ $menu->spiciness_level == 'extra_pedas' ? 'checked' : '' }}
                                class="form-radio h-5 w-5 text-[#811D0E]">
                            <span class="text-lg">Extra Pedas</span>
                        </label>
                    </div>
                </div>
                <div class="flex flex-col w-full gap-2 px-5">
                    <p class="font-semibold">Catatan (Optional)</p>
                    <label
                        class="flex w-full rounded-2xl border border-gray-300 p-[14px_20px] gap-3 bg-white focus-within:ring-1 focus-within:ring-[#811D0E] transition-all duration-300">
                        <textarea name="notes" rows="3"
                            class="appearance-none outline-none w-full font-semibold placeholder:text-ngekos-grey placeholder:font-normal resize-none bg-transparent"
                            placeholder="Tulis catatan tambahan"></textarea>
                    </label>
                </div>
            </div>
            <div class="flex items-center w-full justify-between px-5 mt-5">
                <p class="font-semibold">Jumlah Pesanan</p>
                <div class="relative flex items-center gap-[10px] w-fit">
                    <button type="button" id="Minus" class="w-8 h-8 flex-shrink-0">
                        <img src="{{ asset('assets/images/icons/minus.svg') }}" alt="icon">
                    </button>
                    <input id="Quantity" type="text" value="1" name="quantity"
                        class="appearance-none outline-none !bg-transparent w-[42px] text-center font-semibold text-[18px] leading-[33px]"
                        inputmode="numeric" pattern="[0-9]*">
                    <button type="button" id="Plus" class="w-8 h-8 flex-shrink-0">
                        <img src="{{ asset('assets/images/icons/plus.svg') }}" alt="icon">
                    </button>
                </div>
            </div>
            <div class="items-center justify-between w-full mt-5">
                <button type="submit" id="addOrderBtn"
                    class="flex cursor-pointer items-center justify-center w-full h-[60px] rounded-2xl bg-[#811D0E] px-5">
                    <span class="flex items-center gap-3 font-bold text-white text-xl">
                        Add Order
                        <span id="total-price" class="font-bold">
                            - Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </span>
                    </span>
                </button>
                <a href="{{ route('checkout') }}" id="checkout-btn"
                    style="{{ !session('cart') || count(session('cart')) == 0 ? 'display:none;' : '' }}"
                    class="flex items-center justify-center w-full h-[60px] rounded-2xl bg-ngekos-orange font-bold text-black mt-3">
                    Checkout
                </a>
            </div>
        </form>
        <div id="notif-success"
            class="hidden fixed left-1/2 top-10 z-50 -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg">
            Berhasil ditambahkan ke keranjang!
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const basePrice = parseInt(document.getElementById('base-price').dataset.price);
                const toppingCheckboxes = document.querySelectorAll('.topping-checkbox');
                const totalPriceEl = document.getElementById('total-price');
                const quantityInput = document.getElementById('Quantity');
                const minusBtn = document.getElementById('Minus');
                const plusBtn = document.getElementById('Plus');
                const notifSuccess = document.getElementById('notif-success');
                const checkoutBtn = document.getElementById('checkout-btn');

                function getQuantity() {
                    let qty = parseInt(quantityInput.value);
                    if (isNaN(qty) || qty < 1) qty = 1;
                    return qty;
                }

                function updateTotal() {
                    let menuTotal = basePrice * getQuantity();
                    let toppingTotal = 0;
                    toppingCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            toppingTotal += parseInt(cb.dataset.price);
                        }
                    });
                    let total = menuTotal + toppingTotal;
                    totalPriceEl.innerHTML = '- Rp ' + total.toLocaleString('id-ID') +
                        '<span class="text-sm font-normal">/porsi</span>';
                }

                toppingCheckboxes.forEach(cb => {
                    cb.addEventListener('change', updateTotal);
                });

                quantityInput.addEventListener('input', function() {
                    if (this.value === '' || parseInt(this.value) < 1) this.value = 1;
                    updateTotal();
                });

                plusBtn.addEventListener('click', function() {
                    quantityInput.value = getQuantity() + 1;
                    updateTotal();
                });
                minusBtn.addEventListener('click', function() {
                    if (getQuantity() > 1) {
                        quantityInput.value = getQuantity() - 1;
                        updateTotal();
                    }
                });

                // AJAX Add Order
                document.getElementById('orderForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = e.target;
                    const formData = new FormData(form);

                    fetch("{{ route('cart.add') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                notifSuccess.classList.remove('hidden');
                                setTimeout(() => {
                                    notifSuccess.classList.add('hidden');
                                    window.location.href =
                                        "/"; // Ganti "/" dengan route home/menu list kamu jika perlu
                                }, 1200);
                                if (checkoutBtn) {
                                    checkoutBtn.style.display = 'flex';
                                }
                                updateTotal();
                            } else {
                                alert('Gagal menambah ke keranjang!');
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan, coba lagi.'));
                });

                updateTotal();
            });
        </script>
    </main>
@endsection
