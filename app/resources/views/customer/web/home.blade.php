@extends('layouts.web')
@section('title', 'Ayamo — Ayam Crispy Favorit | Eat, Chill, Repeat')

@section('content')
    <main class="overflow-hidden">
        {{-- Hero Showcase Section --}}
        <section class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
            <div class="relative isolate overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#EA580C] via-[#C2410C] to-[#8C2805] text-white shadow-[0_24px_60px_rgba(194,65,12,0.22)]">
                {{-- Decorative Ambient Glows --}}
                <div class="pointer-events-none absolute -top-32 -left-20 -z-10 h-96 w-96 rounded-full bg-[#FACC15]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 right-1/4 -z-10 h-96 w-96 rounded-full bg-[#EA580C]/40 blur-3xl"></div>

                <div class="grid items-center lg:min-h-[580px] lg:grid-cols-[1.05fr_0.95fr]">
                    {{-- Left Hero Text --}}
                    <div class="relative z-10 flex flex-col justify-center px-6 py-12 sm:px-10 sm:py-16 lg:px-14 lg:py-20">
                        {{-- Tag Badge --}}
                        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-black tracking-wider text-[#FEF08A] uppercase backdrop-blur-md">
                            <span class="h-2 w-2 rounded-full bg-[#FACC15] shadow-[0_0_0_4px_rgba(250,204,21,0.25)]"></span>
                            Ayam Crispy Favorit — Eat, Chill, Repeat
                        </div>

                        {{-- Headline --}}
                        <h1 class="mt-6 font-['Outfit'] text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl xl:text-[4.2rem]">
                            Ayam Crispy Juara,<br>
                            <span class="text-[#FDE047]">Renyah Luar Dalam!</span>
                        </h1>

                        <p class="mt-5 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg">
                            Dibuat dengan bumbu rempah rahasia khas Ayamo dan selalu digoreng hangat mendadak setelah pesananmu masuk. Kriuk maksimal, daging tebal dan juicy!
                        </p>

                        {{-- CTAs --}}
                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <a href="{{ route('web.menu') }}"
                                class="group inline-flex h-14 items-center gap-3 rounded-full bg-[#F3B825] px-8 text-sm font-black text-[#1E1915] shadow-lg shadow-orange-950/20 transition hover:-translate-y-0.5 hover:bg-[#FACC15] active:scale-95">
                                <span>Pesan Sekarang</span>
                                <span class="transition duration-200 group-hover:translate-x-1">→</span>
                            </a>
                            <a href="{{ route('web.menu') }}"
                                class="inline-flex h-14 items-center rounded-full border border-white/30 bg-white/10 px-7 text-sm font-extrabold text-white backdrop-blur-md transition hover:bg-white/20">
                                Lihat Menu Lengkap
                            </a>
                        </div>

                        {{-- Micro Highlights --}}
                        <div class="mt-10 grid max-w-lg grid-cols-3 divide-x divide-white/20 border-t border-white/20 pt-6">
                            <div class="pr-4">
                                <b class="block font-['Outfit'] text-xl font-black text-[#FEF08A]">Fresh</b>
                                <span class="text-xs font-medium text-white/70">Digoreng mendadak</span>
                            </div>
                            <div class="px-4">
                                <b class="block font-['Outfit'] text-xl font-black text-[#FEF08A]">Mulai Rp500</b>
                                <span class="text-xs font-medium text-white/70">Harga ramah kantong</span>
                            </div>
                            <div class="pl-4">
                                <b class="block font-['Outfit'] text-xl font-black text-[#FEF08A]">Cepat</b>
                                <span class="text-xs font-medium text-white/70">Pesan tanpa antre</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Hero Visual --}}
                    <div class="relative flex items-center justify-center p-6 sm:p-10 lg:p-8">
                        <div class="relative w-full max-w-[460px] overflow-hidden rounded-[2rem] border-4 border-white/20 bg-[#6C1F05]/50 shadow-2xl backdrop-blur-sm">
                            <img src="{{ asset('img/hero-chicken-mockup.png') }}"
                                alt="Ayam Crispy Hangat Ayamo"
                                class="h-[340px] w-full object-cover object-center sm:h-[400px]">

                            {{-- Floating Chip 1: Rating --}}
                            <div class="absolute top-4 left-4 flex items-center gap-2 rounded-full border border-white/30 bg-[#1E1915]/85 px-4 py-2 text-xs font-extrabold text-white shadow-lg backdrop-blur-md">
                                <span class="text-[#FACC15]">⭐</span>
                                <span>4.9/5 · 1.200+ Pesanan</span>
                            </div>

                            {{-- Floating Chip 2: Price Starting --}}
                            <div class="absolute right-4 bottom-4 rounded-[1.2rem] border border-white/30 bg-white/95 px-4 py-2.5 text-[#1E1915] shadow-xl backdrop-blur-md">
                                <span class="block text-[10px] font-black tracking-wider text-[#EA580C] uppercase">Mulai Dari</span>
                                <p class="font-['Outfit'] text-xl font-black leading-none text-[#B83809]">Rp500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 6 Quick Category Shortcuts --}}
        @php
            $quickCategories = [
                ['label' => 'Dada Ayam', 'query' => 'Dada', 'img' => 'img/cat-dada.png', 'desc' => 'Daging tebal juicy'],
                ['label' => 'Paha Atas & Bawah', 'query' => 'Paha', 'img' => 'img/cat-paha.png', 'desc' => 'Favorit gurih renyah'],
                ['label' => 'Sayap Ayam', 'query' => 'Sayap', 'img' => 'img/cat-sayap.png', 'desc' => 'Kriuk pas buat ngemil'],
                ['label' => 'Ceker Ayam', 'query' => 'Ceker', 'img' => 'img/cat-ceker.png', 'desc' => 'Bumbu gurih meresap'],
                ['label' => 'Leher Ayam', 'query' => 'Leher', 'img' => 'img/cat-leher.png', 'desc' => 'Crispy bikin nagih'],
                ['label' => 'Bakso Stick', 'query' => 'Bakso', 'img' => 'img/cat-bakso.png', 'desc' => 'Camilan renyah hemat'],
            ];
        @endphp
        <section class="mx-auto max-w-7xl px-4 pt-14 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">PILIHAN KATEGORI</span>
                    <h2 class="font-['Outfit'] text-2xl font-black text-[#1E1915] sm:text-3xl">Mau Potongan Apa Hari Ini?</h2>
                </div>
                <a href="{{ route('web.menu') }}" class="text-xs font-extrabold text-[#B83809] hover:underline">
                    Lihat Semua Menu →
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 sm:gap-4">
                @foreach ($quickCategories as $cat)
                    <a href="{{ route('web.menu', ['q' => $cat['query']]) }}"
                        class="group flex flex-col items-center rounded-[20px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 text-center shadow-2xs transition duration-200 hover:-translate-y-1 hover:border-[#EA580C]/50 hover:shadow-md">
                        <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-[16px] bg-[#ECC59A]/40 p-2 transition duration-200 group-hover:scale-105">
                            <img src="{{ asset($cat['img']) }}" alt="{{ $cat['label'] }}" class="h-full w-full object-contain">
                        </div>
                        <h3 class="mt-3 font-['Outfit'] text-sm font-black text-[#1E1915] group-hover:text-[#EA580C]">{{ $cat['label'] }}</h3>
                        <p class="mt-0.5 text-[11px] font-semibold text-[#8C7E70]">{{ $cat['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Favorite Menu Section --}}
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="h-1 w-6 rounded-full bg-[#EA580C]"></span>
                        <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">MENU PALING DISUKAI</span>
                    </div>
                    <h2 class="mt-2 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">
                        Menu Favorit Pelanggan Ayamo
                    </h2>
                    <p class="mt-2 text-sm text-[#7C6D5D]">
                        Paling sering dipesan, selalu disajikan hangat dengan bumbu gurih yang meresap sempurna.
                    </p>
                </div>
                <a href="{{ route('web.menu') }}"
                    class="group inline-flex items-center gap-2 rounded-full border border-[#D8CBB8] bg-white px-5 py-2.5 text-xs font-extrabold text-[#1E1915] shadow-2xs transition hover:border-[#EA580C] hover:text-[#EA580C]">
                    <span>Buka Katalog Lengkap</span>
                    <span class="transition duration-150 group-hover:translate-x-0.5">→</span>
                </a>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($favorites as $product)
                    @include('customer.web.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>

        {{-- Value Proposition (Kenapa Ayamo?) --}}
        <section class="border-y border-[#EBE1D0] bg-[#FAF5EB]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    {{-- Point 1 --}}
                    <div class="flex items-start gap-4 rounded-2xl bg-[#FFFDF9] p-5 border border-[#EBE1D0] shadow-2xs">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#B83809] text-2xl text-white shadow-2xs">
                            🍗
                        </div>
                        <div>
                            <h3 class="font-['Outfit'] text-base font-black text-[#1E1915]">Digoreng Fresh</h3>
                            <p class="mt-1 text-xs leading-relaxed text-[#7C6D5D]">
                                Baru dimasak setelah pesanan masuk. Bukan ayam pajangan yang sudah dingin, kriuknya tahan lama.
                            </p>
                        </div>
                    </div>

                    {{-- Point 2 --}}
                    <div class="flex items-start gap-4 rounded-2xl bg-[#FFFDF9] p-5 border border-[#EBE1D0] shadow-2xs">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#F3B825] text-2xl text-[#1E1915] shadow-2xs">
                            💰
                        </div>
                        <div>
                            <h3 class="font-['Outfit'] text-base font-black text-[#1E1915]">Harga Ramah Kantong</h3>
                            <p class="mt-1 text-xs leading-relaxed text-[#7C6D5D]">
                                Nikmat dan hemat mulai dari Rp500. Pas untuk santap siang, makan malam, maupun camilan bareng teman.
                            </p>
                        </div>
                    </div>

                    {{-- Point 3 --}}
                    <div class="flex items-start gap-4 rounded-2xl bg-[#FFFDF9] p-5 border border-[#EBE1D0] shadow-2xs">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#16A34A] text-2xl text-white shadow-2xs">
                            ⚡
                        </div>
                        <div>
                            <h3 class="font-['Outfit'] text-base font-black text-[#1E1915]">Pesan Cepat Tanpa Antre</h3>
                            <p class="mt-1 text-xs leading-relaxed text-[#7C6D5D]">
                                Pilih menu online, pilih metode bayar tunai atau transfer, lalu tinggal ambil saat pesananmu siap.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call To Action Banner --}}
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-[#9A3412] via-[#B83809] to-[#7C2D12] p-8 text-white shadow-xl sm:p-12 lg:flex lg:items-center lg:justify-between">
                {{-- Decorative circle --}}
                <div class="pointer-events-none absolute -right-12 -bottom-20 h-64 w-64 rounded-full bg-[#F3B825]/20 blur-2xl"></div>

                <div class="relative z-10 max-w-2xl">
                    <span class="inline-block rounded-full bg-[#F3B825] px-3.5 py-1 text-xs font-black text-[#1E1915]">
                        Lagi Lapar Sekarang?
                    </span>
                    <h2 class="mt-3 font-['Outfit'] text-3xl font-black sm:text-4xl">
                        Ayam Crispy Hangat Siap Menemani Harimu.
                    </h2>
                    <p class="mt-2 text-sm text-white/85">
                        Pesan lewat web sekarang, ambil di outlet tanpa perlu menunggu antrean panjang.
                    </p>
                </div>

                <div class="relative z-10 mt-6 lg:mt-0">
                    <a href="{{ route('web.menu') }}"
                        class="inline-flex h-14 items-center gap-2.5 rounded-full bg-[#F3B825] px-8 text-sm font-black text-[#1E1915] shadow-lg transition hover:bg-[#FACC15] active:scale-95">
                        <span>Mulai Pesan Sekarang</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
@endsection
