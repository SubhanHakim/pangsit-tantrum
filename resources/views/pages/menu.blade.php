@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    <div id="Background" class="absolute top-0 w-full h-[240px] bg-[linear-gradient(180deg,#FAFAFA_0%,#C8C8C8_100%)]">
    </div>
    <div id="TopNav" class="relative flex items-center justify-between px-5 mt-[60px]">
        <div class="flex flex-col gap-1">
            <p>Pilih Menu</p>
            <h1 class="font-bold text-xl leading-[30px]">Pangsit Tantrum</h1>
        </div>
        <a href="{{ route('checkout') }}" class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white">
            <img src="{{ asset('assets/images/icons/cart.svg') }}" class="w-[28px] h-[28px]" alt="icon">
        </a>
    </div>
    
    <!-- Categories Bar -->
    <div id="CategoriesBar" class="relative mt-[40px] px-5">
        <h2 class="font-bold text-lg">Kategori</h2>
        <div class="flex gap-4 overflow-x-auto py-4 no-scrollbar">
            @foreach ($categories as $category)
                <a href="#{{ $category->name }}" class="flex-shrink-0 px-4 py-2 rounded-full bg-white shadow-[0px_4px_10px_0px_#0000000D] text-sm">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
    
    <!-- Featured Menu -->
    <div id="Featured" class="relative mt-4 px-5">
        <h2 class="font-bold text-lg mb-4">Menu Favorit</h2>
        <div class="grid grid-cols-2 gap-4">
            @foreach ($featuredMenus as $menu)
                <a href="{{ route('menu.show', $menu->id) }}" class="block">
                    <div class="flex flex-col rounded-[12px] overflow-hidden bg-white shadow-[0px_12px_30px_0px_#0000000D]">
                        <div class="w-full h-[120px] overflow-hidden">
                            <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover" alt="{{ $menu->name }}">
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-sm">{{ $menu->name }}</h3>
                            <p class="text-[#811D0E] font-bold mt-1">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    
    <!-- Menu By Categories -->
    @foreach ($categories as $category)
        <div id="{{ $category->name }}" class="relative mt-8 px-5">
            <h2 class="font-bold text-lg mb-4">{{ $category->name }}</h2>
            <div class="grid grid-cols-2 gap-4">
                @foreach ($category->menus as $menu)
                    <a href="{{ route('menu.show', $menu->id) }}" class="block">
                        <div class="flex flex-col rounded-[12px] overflow-hidden bg-white shadow-[0px_12px_30px_0px_#0000000D]">
                            <div class="w-full h-[120px] overflow-hidden">
                                <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover" alt="{{ $menu->name }}">
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-sm">{{ $menu->name }}</h3>
                                <p class="text-[#811D0E] font-bold mt-1">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
    
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
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;  /* Chrome, Safari, Opera */
    }
</style>
@endpush