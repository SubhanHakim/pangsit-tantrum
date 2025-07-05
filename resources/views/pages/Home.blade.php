@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div id="Background" class="absolute top-0 w-full h-[240px] bg-[linear-gradient(180deg,#FAFAFA_0%,#C8C8C8_100%)]">
    </div>
    <div id="TopNav" class="relative flex items-center justify-between px-5 mt-[60px]">
        <div class="flex flex-col gap-1">
            <p>Selamat Datang Di</p>
            <h1 class="font-bold text-xl leading-[30px]">Pangsit Tantrum</h1>
        </div>
        <a href="#" class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-white">
            <img src="assets/images/icons/notification.svg" class="w-[28px] h-[28px]" alt="icon">
        </a>
    </div>
    <div id="Categories" class="swiper w-full overflow-x-hidden mt-[40px]">
        <div class="swiper-wrapper flex gap-5 px-5">
            @foreach ($categories as $category)
                <div class="swiper-slide !w-fit pb-[50px]">
                    <a href="{{ route('menu.category', $category->id) }}" class="card">
                        <div
                            class="flex flex-col items-center w-[120px] shrink-0 rounded-[8px] p-4 pb-5 gap-3 bg-white shadow-[0px_12px_30px_0px_#0000000D] text-center">
                            <div class="w-[80px] h-[80px] rounded-[8px] flex shrink-0 overflow-hidden">
                                <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-full object-cover"
                                    alt="thumbnail">
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <h3 class="font-semibold">{{ $category->name }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @include('section.makanan')
    @include('section.minuman')
    @if (session('cart') && count(session('cart')) > 0)
        <a href="{{ route('checkout') }}"
            class="fixed bottom-5 left-0 right-0 mx-auto max-w-[640px] w-full px-5 z-50 flex items-center justify-center h-[60px] rounded-2xl bg-[#811D0E] font-bold text-white shadow-lg">
            Checkout
        </a>
    @endif
@endsection
