@extends('layouts.mobile')
@section('title', 'Beranda | Ayamo Mobile')
@section('content')
    {{-- Scrollable Beranda Content --}}
    <main class="flex-1 px-4 pt-4 pb-32">
        {{-- Top App Header --}}
        <header class="mb-4 pt-1">
            <div class="flex items-center justify-between">
                <a href="{{ route('mobile.home') }}" class="flex items-center gap-3 focus:outline-none">
                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-[18px] border border-[#F3E3D0] bg-[#FFF8F0] p-1 shadow-2xs transition hover:scale-105 active:scale-95">
                        <img src="{{ asset('img/logo.webp') }}" alt="Ayamo Logo" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-['Outfit'] text-[24px] font-black leading-none tracking-tight text-[#EA580C]">
                                Ayamo
                            </span>
                            <span class="rounded-full bg-[#FEF3C7] px-2 py-0.5 text-[10px] font-extrabold text-[#92400E]">
                                Crispy
                            </span>
                        </div>
                        <p class="mt-0.5 text-[11px] font-extrabold tracking-wide text-[#7C6D5D]">Eat, Chill, Repeat</p>
                    </div>
                </a>

                {{-- Status Buka Toko --}}
                <div class="flex items-center gap-1.5 rounded-full border border-[#DCFCE7] bg-[#F0FDF4] px-3 py-1 text-[11px] font-bold text-[#166534] shadow-2xs">
                    <span class="h-2 w-2 rounded-full bg-[#22C55E]"></span>
                    <span>Buka</span>
                </div>
            </div>

            <p class="mt-3 text-[13px] font-bold text-[#6D5D4E]">Mau makan ayam apa hari ini?</p>
        </header>

        {{-- Search Bar --}}
        <form action="{{ route('mobile.menu') }}" method="get" class="mb-4" role="search">
            <label class="flex items-center gap-3 rounded-[18px] border border-[#E5CEAC] bg-[#FDF8EE] px-4 py-3 shadow-2xs transition focus-within:border-[#CC4714] focus-within:bg-white">
                <svg class="h-5 w-5 shrink-0 text-[#6E6256]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                </svg>
                <input type="search" name="q" aria-label="Cari menu" autocomplete="off" placeholder="Cari dada, paha, sayap..."
                    class="w-full bg-transparent text-xs font-semibold text-[#1E1915] placeholder-[#8A7D6F] focus:outline-none">
            </label>
        </form>

        {{-- Hero Promo Banner matching beranda mobile.png --}}
        <section class="relative mb-4 overflow-hidden rounded-[22px] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-4 text-white shadow-md">
            <div class="relative z-10 max-w-[58%]">
                {{-- Title with yellow comic rays --}}
                <div class="relative inline-block -rotate-2 pl-2">
                    <svg class="pointer-events-none absolute -top-1 -left-2 h-6 w-6 text-[#FACC15]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                        <path d="M12 3v5M4 8l4 3M2 15h5" />
                    </svg>
                    <span class="block font-['Outfit'] text-[22px] leading-none font-black tracking-tight text-white drop-shadow-xs">
                        Ayam Crispy
                    </span>
                    <span class="block pl-2 font-['Outfit'] text-[24px] leading-tight font-black tracking-tight text-[#FDE047] drop-shadow-xs">
                        Favorit
                    </span>
                    <svg class="pointer-events-none absolute top-2 -right-5 h-6 w-6 text-[#FACC15]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                        <path d="M16 6l4-3M17 12l5 1" />
                    </svg>
                </div>

                <p class="mt-1.5 text-[10px] leading-snug font-medium text-white/95">
                    Renyah di luar, lembut di dalam.<br>Selalu jadi pilihan!
                </p>

                <a href="{{ route('mobile.menu') }}"
                    class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-[#F3B825] px-4 py-1.5 text-[11px] font-extrabold text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95">
                    <span>Pesan Sekarang</span>
                    <span>→</span>
                </a>
            </div>

            {{-- Right Chicken Platter Photo --}}
            <div class="absolute -top-2 -right-4 -bottom-2 w-[50%] overflow-hidden rounded-l-[64px] border-l-4 border-[#9A3412]/50 bg-[#6A2006] shadow-inner">
                <img src="{{ asset('img/hero-chicken-mockup.png') }}" alt="Ayam Crispy Favorit" class="h-full w-full object-cover object-center">
            </div>
        </section>

        {{-- 6 Kategori Cepat (Dada, Paha, Ceker, Sayap, Leher, Bakso) matching beranda mobile.png --}}
        @php
            $quickCategories = [
                ['label' => 'Dada', 'query' => 'Dada', 'img' => 'img/cat-dada.png'],
                ['label' => 'Paha', 'query' => 'Paha', 'img' => 'img/cat-paha.png'],
                ['label' => 'Ceker', 'query' => 'Ceker', 'img' => 'img/cat-ceker.png'],
                ['label' => 'Sayap', 'query' => 'Sayap', 'img' => 'img/cat-sayap.png'],
                ['label' => 'Leher', 'query' => 'Leher', 'img' => 'img/cat-leher.png'],
                ['label' => 'Bakso', 'query' => 'Bakso', 'img' => 'img/cat-bakso.png'],
            ];
        @endphp
        <section class="mb-5 grid grid-cols-6 gap-2">
            @foreach ($quickCategories as $cat)
                <a href="{{ route('mobile.menu', ['q' => $cat['query']]) }}" class="group flex flex-col items-center">
                    <div class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-[16px] bg-[#ECC59A] p-1.5 shadow-2xs transition duration-150 group-hover:-translate-y-0.5 group-active:scale-95">
                        <img src="{{ asset($cat['img']) }}" alt="{{ $cat['label'] }}" class="h-full w-full object-contain">
                    </div>
                    <span class="mt-1.5 text-[11px] font-semibold text-[#1E1915]">{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </section>

        {{-- Menu Favorit --}}
        <section class="mb-5">
            <div class="mb-2.5 flex items-center justify-between">
                <h2 class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">Menu Favorit</h2>
                <a href="{{ route('mobile.menu') }}" class="text-[11px] font-bold text-[#B83809] hover:underline">Lihat semua →</a>
            </div>
            <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1.5">
                @foreach ($favorites as $product)
                    @php
                        $inCartQty = (int) ($cart[$product->id] ?? 0);
                    @endphp
                    <article class="flex w-[124px] shrink-0 flex-col rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-2.5 transition hover:shadow-sm">
                        <div class="mb-2 flex aspect-square w-full items-center justify-center overflow-hidden rounded-[16px] bg-[#ECC59A] p-2">
                            <img src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-contain transition duration-200 hover:scale-105">
                        </div>
                        <h3 class="truncate pl-0.5 text-xs font-extrabold text-[#1E1915]">{{ $product->name }}</h3>
                        <p class="mb-2.5 pl-0.5 text-xs font-extrabold text-[#1E1915]">{{ number_format($product->price, 0, ',', '.') }}</p>
                        <form method="post" action="{{ route('mobile.cart.add') }}"
                            data-ajax-cart
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->price }}"
                            data-product-stock="{{ $product->stock }}"
                            class="mt-auto">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit"
                                data-product-btn="{{ $product->id }}"
                                @disabled($product->stock < 1)
                                class="w-full cursor-pointer rounded-full bg-[#ECC59A] py-1.5 text-xs font-extrabold text-[#1E1915] transition hover:bg-[#E2B37E] active:scale-95 disabled:opacity-50">
                                {{ $inCartQty > 0 ? 'Tambah (' . $inCartQty . ')' : 'Tambah' }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Info Toko --}}
        <section class="rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4">
            <h2 class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">Info Toko</h2>
            <p class="mt-1 text-xs leading-relaxed text-[#574C40]">
                {{ $settings['address'] ?? 'Alamat toko Ayamo Jl.prof.dr.sharif thayib-gp teungoh. Kec. Langsa kota.kota langsa' }}
            </p>
            <p class="mt-0.5 text-xs font-medium text-[#574C40]">
                Buka setiap hari {{ $settings['store_time'] ?? '15.00 - 03.00' }}
            </p>
        </section>
    </main>

    @include('layouts.navbar', ['activeTab' => 'home'])
@endsection
