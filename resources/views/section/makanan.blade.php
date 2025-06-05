<section id="makanan" class="flex flex-col gap-4">
        <div class="flex items-center justify-between px-5">
            <h2 class="font-bold">Menu Makanan</h2>
            <a href="#">
                <div class="flex items-center gap-2">
                    <span>Lihat Semua</span>
                    <img src="assets/images/icons/arrow-right.svg" class="w-6 h-6 flex shrink-0" alt="icon">
                </div>
            </a>
        </div>
        <div class="swiper w-full overflow-x-hidden">
            <div class="swiper-wrapper">
                @foreach ($menuMakanans as $menuMakanan)
                    <div class="swiper-slide !w-fit">
                        <a href="{{ route('menu.show', $menuMakanan->id) }}" class="card">
                            <div
                                class="flex flex-col w-[250px] shrink-0 rounded-[30px] border border-[#F1F2F6] p-4 pb-5 gap-[10px] hover:border-[#811D0E] transition-all duration-300">
                                <div class="flex w-full h-[150px] shrink-0 rounded-[30px] bg-[#D9D9D9] overflow-hidden">
                                    <img src="{{ asset('storage/' . $menuMakanan->image) }}" class="w-full h-full object-cover"
                                        alt="thumbnail">
                                </div>
                                <div class="flex flex-col gap-3">
                                    <h3 class="font-semibold text-lg leading-[27px] line-clamp-2 min-h-[54px]">
                                        {{ $menuMakanan->name }}</h3>
                                    <hr class="border-[#F1F2F6]">
                                    <div class="flex items-center gap-[6px]">
                                        <img src="assets/images/icons/3dcube.svg" class="w-5 h-5 flex shrink-0"
                                            alt="icon">
                                        <p class="text-sm text-ngekos-grey">{{ $menuMakanan->category->name }}</p>
                                    </div>
                                    <p class="font-semibold text-lg text-ngekos-orange">Rp
                                        {{ number_format($menuMakanan->price, 0, ',', '.') }} <span
                                            class="text-sm text-ngekos-grey font-normal">/pack</span>
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
    </section>