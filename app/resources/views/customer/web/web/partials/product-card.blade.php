<article class="group flex h-full flex-col justify-between overflow-hidden rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] shadow-2xs transition duration-300 hover:-translate-y-1 hover:border-[#EA580C]/45 hover:shadow-[0_18px_38px_-16px_rgba(184,56,9,0.22)]">
    {{-- Product Image Surface --}}
    <div class="relative m-3 flex aspect-[1.18/1] items-center justify-center overflow-hidden rounded-[20px] bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] p-4">
        {{-- Category Chip --}}
        <span class="absolute top-3 left-3 z-10 rounded-full border border-white/70 bg-white/92 px-2.5 py-1 text-[10px] font-black tracking-wider text-[#7C2D12] uppercase shadow-2xs backdrop-blur-md">
            {{ $product->category }}
        </span>

        {{-- Stock Chip --}}
        @if ($product->stock < 1)
            <span class="absolute top-3 right-3 z-10 rounded-full bg-[#DC2626] px-2.5 py-1 text-[10px] font-black text-white shadow-2xs">
                Habis
            </span>
        @elseif ($product->stock <= 10)
            <span class="absolute top-3 right-3 z-10 rounded-full bg-[#F3B825] px-2.5 py-1 text-[10px] font-black text-[#1E1915] shadow-2xs">
                Sisa {{ $product->stock }}
            </span>
        @else
            <span class="absolute top-3 right-3 z-10 inline-flex items-center gap-1 rounded-full border border-[#DCFCE7] bg-[#F0FDF4]/95 px-2.5 py-1 text-[10px] font-extrabold text-[#166534] shadow-2xs backdrop-blur-md">
                <span class="h-1.5 w-1.5 rounded-full bg-[#22C55E]"></span>
                Tersedia
            </span>
        @endif

        {{-- Product Graphic --}}
        <img src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-contain transition duration-300 {{ $product->stock < 1 ? 'opacity-45 grayscale' : 'group-hover:scale-110' }}"
            loading="lazy"
            decoding="async">
    </div>

    {{-- Product Details --}}
    <div class="flex flex-1 flex-col px-4 pt-1 pb-4">
        <h3 class="font-['Outfit'] text-lg font-black tracking-tight text-[#1E1915] transition-colors group-hover:text-[#EA580C]">
            {{ $product->name }}
        </h3>
        <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-[#7C6D5D]">
            {{ $product->description }}
        </p>

        {{-- Price & Add to Cart Action --}}
        <div class="mt-auto pt-4">
            <div class="flex items-center justify-between gap-2 border-t border-[#EFE6D8] pt-3.5">
                <div>
                    <span class="block text-[10px] font-bold tracking-wider text-[#9A8D7E] uppercase">Harga</span>
                    <strong class="font-['Outfit'] text-lg font-black text-[#B83809]">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </strong>
                </div>

                <form method="post" action="{{ route('web.cart.add') }}" data-local-cart
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-price="{{ $product->price }}"
                    data-product-stock="{{ $product->stock }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" data-product-btn="{{ $product->id }}"
                        @disabled($product->stock < 1)
                        aria-label="Tambah {{ $product->name }} ke keranjang"
                        class="inline-flex h-10 cursor-pointer items-center gap-1.5 rounded-full bg-[#F3B825] px-4 text-xs font-black text-[#1E1915] shadow-2xs transition hover:bg-[#FACC15] active:scale-95 disabled:cursor-not-allowed disabled:bg-[#E5D7C3] disabled:text-[#7C6D5D]">
                        @if ($product->stock < 1)
                            <span>Habis</span>
                        @else
                            <span class="text-sm leading-none">+</span>
                            <span data-product-btn-label>Tambah</span>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
</article>
