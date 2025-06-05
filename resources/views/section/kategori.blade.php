<div id="Categories" class="swiper w-full overflow-x-hidden mt-[40px]">
        <div class="swiper-wrapper flex gap-5 px-5">
            @foreach ($categories as $category)
                <div class="swiper-slide !w-fit pb-[50px]">
                    <a href="categories.html" class="card">
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