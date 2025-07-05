@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div id="Background" class="absolute top-0 w-full h-[240px] bg-[linear-gradient(180deg,#FAFAFA_0%,#C8C8C8_100%)]">
    </div>
    <div id="TopNav" class="relative flex items-center justify-between px-5 mt-[60px]">
        <a href="/" class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white">
            <img src="{{ asset('assets/images/icons/tabler_arrow-left.svg') }}" class="w-8 h-8" alt="back">
        </a>
        <div class="flex flex-col gap-1 text-center">
            <p>Menu</p>
            <h1 class="font-bold text-xl leading-[30px]">{{ $category->name }}</h1>
        </div>
        <a href="{{ route('checkout') }}"
            class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white">
            <img src="{{ asset('assets/images/icons/cart.svg') }}" class="w-[28px] h-[28px]" alt="cart">
        </a>
    </div>

    <!-- Categories Bar for Quick Navigation -->
    <div id="CategoriesBar" class="relative mt-[40px] px-5">
        <h2 class="font-bold text-lg mb-4">Kategori Lainnya</h2>
        <div class="flex gap-4 overflow-x-auto py-4 no-scrollbar">
            @foreach ($categories as $cat)
                <a href="{{ route('menu.category', $cat->id) }}"
                    class="flex-shrink-0 px-4 py-2 rounded-full shadow-[0px_4px_10px_0px_#0000000D] text-sm {{ $cat->id == $category->id ? 'bg-[#811D0E] text-white' : 'bg-white' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Menu Items -->
    <div id="MenuItems" class="mt-8 px-5">
        @if ($menus->count() > 0)
            <div class="swiper w-full overflow-x-hidden">
                <div class="swiper-wrapper">
                    @foreach ($menus as $menu)
                        <div class="swiper-slide !w-fit">
                            <a href="{{ route('menu.show', $menu->id) }}" class="card">
                                <div
                                    class="flex flex-col w-[250px] shrink-0 rounded-[30px] border border-[#F1F2F6] p-4 pb-5 gap-[10px] hover:border-[#811D0E] transition-all duration-300">
                                    <div class="flex w-full h-[150px] shrink-0 rounded-[30px] bg-[#D9D9D9] overflow-hidden">
                                        <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover"
                                            alt="thumbnail">
                                    </div>
                                    <div class="flex flex-col gap-3">
                                        <h3 class="font-semibold text-lg leading-[27px] line-clamp-2 min-h-[54px]">
                                            {{ $menu->name }}
                                        </h3>
                                        <hr class="border-[#F1F2F6]">
                                        <div class="flex items-center gap-[6px]">
                                            <img src="{{ asset('assets/images/icons/3dcube.svg') }}"
                                                class="w-5 h-5 flex shrink-0" alt="icon">
                                            <p class="text-sm text-ngekos-grey">{{ $menu->category->name }}</p>
                                        </div>
                                        <p class="font-semibold text-lg text-ngekos-orange">Rp
                                            {{ number_format($menu->price, 0, ',', '.') }}
                                            <span class="text-sm text-ngekos-grey font-normal">/pack</span>
                                        </p>
                                        <hr class="border-[#F1F2F6]">
                                        <div>
                                            <button
                                                class="w-full h-10 rounded-lg border-2 border-[#811D0E] text-[#811D0E] font-semibold bg-transparent hover:bg-[#811D0E] hover:text-white transition">
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-gray-500">Belum ada menu di kategori ini.</p>
                <a href="/" class="mt-4 inline-block px-6 py-2 bg-[#811D0E] text-white rounded-lg">Kembali ke Home</a>
            </div>
        @endif
    </div>

    <!-- Floating Cart Button -->
    @if (session('cart') && count(session('cart')) > 0)
        <a href="{{ route('checkout') }}"
            class="fixed bottom-5 left-0 right-0 mx-auto max-w-[640px] w-full px-5 z-50 flex items-center justify-center h-[60px] rounded-2xl bg-[#811D0E] font-bold text-white shadow-lg">
            Checkout
        </a>
    @endif

    <!-- Spacing for floating button -->
    <div class="h-20"></div>
@endsection

@push('styles')
    <style>
        /* Hide scrollbar but allow scrolling */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }
    </style>
@endpush
