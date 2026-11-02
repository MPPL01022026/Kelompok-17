@extends('layouts.mobile')
@section('title', 'Menu | Ayamo Mobile')
@section('content')
    <main class="flex-1 px-4 pt-4 pb-32">
        {{-- Search Bar --}}
        <form action="{{ route('mobile.menu') }}" method="get" class="mb-3" role="search">
            <label class="flex items-center gap-3 rounded-[18px] border border-[#E5CEAC] bg-[#FDF8EE] px-4 py-3 shadow-2xs transition focus-within:border-[#CC4714] focus-within:bg-white">
                <svg class="h-5 w-5 shrink-0 text-[#6E6256]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                </svg>
                <input type="search" name="q" value="{{ $search ?? '' }}" aria-label="Cari menu" autocomplete="off" placeholder="Cari dada, paha, sayap..."
                    class="w-full bg-transparent text-xs font-semibold text-[#1E1915] placeholder-[#8A7D6F] focus:outline-none">
                @if (!empty($search))
                    <a href="{{ route('mobile.menu') }}" class="shrink-0 rounded-full bg-[#EFE4D2] px-2.5 py-0.5 text-[11px] font-bold text-[#6E5D4F] hover:bg-[#E4D4BC]">Reset</a>
                @endif
            </label>
        </form>

        {{-- Menu List matching Menu mobile.png --}}
        <div class="divide-y divide-[#EFE6D8]">
            @forelse ($products as $product)
                @php
                    $inCartQty = (int) ($cart[$product->id] ?? 0);
                @endphp
                <article class="flex items-center justify-between gap-4 py-4">
                    <div class="min-w-0 flex-1 pr-2">
                        <h3 class="font-['Outfit'] text-[16px] font-extrabold text-[#1E1915]">{{ $product->name }}</h3>
                        <p class="mt-0.5 text-xs leading-relaxed text-[#8C7E70]">{{ $product->description }}</p>
                        <strong class="mt-2.5 block font-['Outfit'] text-sm font-extrabold text-[#1E1915]">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="relative shrink-0 pb-3">
                        <div class="flex h-[84px] w-[92px] items-center justify-center overflow-hidden rounded-[20px] bg-[#FBF0C4] p-1.5 shadow-2xs">
                            <img src="{{ str_starts_with($product->image_url ?? '', '/') ? asset(ltrim($product->image_url, '/')) : ($product->image_url ?: asset('img/menu-drumstick.png')) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover rounded-[14px]">
                        </div>

                        <form method="post" action="{{ route('mobile.cart.add') }}"
                            data-ajax-cart
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->price }}"
                            data-product-stock="{{ $product->stock }}"
                            class="absolute -bottom-0.5 left-1/2 w-[94%] -translate-x-1/2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit"
                                data-product-btn="{{ $product->id }}"
                                @disabled($product->stock < 1)
                                class="w-full cursor-pointer rounded-xl border-[1.8px] border-[#B83809] bg-white py-1 text-xs font-extrabold text-[#B83809] shadow-2xs transition hover:bg-[#FFF5EE] active:scale-95 disabled:opacity-50">
                                {{ $inCartQty > 0 ? 'Tambah (' . $inCartQty . ')' : 'Tambah' }}
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="my-6 rounded-[22px] border border-dashed border-[#D8CBB8] p-8 text-center">
                    <p class="text-sm font-bold text-[#7C6D5D]">Menu "{{ $search }}" tidak ditemukan.</p>
                    <a href="{{ route('mobile.menu') }}" class="mt-2 inline-block text-xs font-extrabold text-[#B83809] underline">Tampilkan semua menu</a>
                </div>
            @endforelse
        </div>
    </main>

    @include('layouts.navbar', ['activeTab' => 'menu'])
@endsection
