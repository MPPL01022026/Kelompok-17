@extends('layouts.web')
@section('title', 'Katalog Menu — Ayamo | Ayam Crispy Favorit')

@section('content')
    <main class="mx-auto min-h-[65vh] max-w-7xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        {{-- Banner Header --}}
        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-8 text-white shadow-md sm:p-10">
            {{-- Decorative glow --}}
            <div class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full bg-[#F3B825]/20 blur-2xl"></div>

            <div class="relative z-10">
                <span class="inline-block rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-black text-[#92400E]">
                    KATALOG LENGKAP
                </span>
                <div class="mt-3 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="font-['Outfit'] text-3xl font-black sm:text-4xl lg:text-5xl">
                            Pilih Menu Favoritmu
                        </h1>
                        <p class="mt-2 text-sm text-white/85">
                            Semua varian digoreng hangat dan renyah setelah pesananmu masuk.
                        </p>
                    </div>

                    {{-- Search Input Form --}}
                    <form method="get" class="flex w-full max-w-md gap-2">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-[#8C7E70]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                            </svg>
                            <input name="q" value="{{ $search }}" placeholder="Cari dada, paha, sayap..."
                                class="h-12 w-full rounded-2xl border border-white/20 bg-white pl-10 pr-4 text-xs font-semibold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:ring-2 focus:ring-[#F3B825]">
                        </div>
                        <button type="submit"
                            class="cursor-pointer rounded-2xl bg-[#F3B825] px-6 text-xs font-black text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="mt-8 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('web.menu', array_filter(['q' => $search])) }}"
                class="rounded-full px-5 py-2 text-xs font-black transition {{ $category === '' ? 'bg-[#B83809] text-white shadow-xs' : 'border border-[#EADFC9] bg-[#FFFDF9] text-[#6B5E50] hover:border-[#B83809] hover:text-[#B83809]' }}">
                Semua Menu
            </a>
            @foreach ($categories as $itemCategory)
                <a href="{{ route('web.menu', array_filter(['q' => $search, 'category' => $itemCategory])) }}"
                    class="rounded-full px-5 py-2 text-xs font-black transition {{ $category === $itemCategory ? 'bg-[#B83809] text-white shadow-xs' : 'border border-[#EADFC9] bg-[#FFFDF9] text-[#6B5E50] hover:border-[#B83809] hover:text-[#B83809]' }}">
                    {{ $itemCategory }}
                </a>
            @endforeach
        </div>

        {{-- Product Grid or Empty State --}}
        @if ($products->isEmpty())
            <div class="mt-10 rounded-[2.5rem] border border-dashed border-[#D8CBB8] bg-[#FFFDF9] px-6 py-20 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#FBF0C4] text-3xl">
                    🔍
                </div>
                <h2 class="mt-4 font-['Outfit'] text-2xl font-black text-[#1E1915]">Menu Tidak Ditemukan</h2>
                <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Coba gunakan kata kunci lain atau tampilkan seluruh menu.</p>
                <a href="{{ route('web.menu') }}"
                    class="mt-6 inline-block rounded-2xl bg-[#B83809] px-6 py-3 text-xs font-black text-white shadow-xs transition hover:bg-[#992D06]">
                    Lihat Semua Menu
                </a>
            </div>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($products as $product)
                    @include('customer.web.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </main>
@endsection
