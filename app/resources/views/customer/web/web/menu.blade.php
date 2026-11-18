@extends('layouts.web')
@section('title', 'Katalog Menu — Ayamo | Ayam Crispy Favorit')

@section('content')
    <main id="main-content" tabindex="-1"
        class="mx-auto min-h-[65vh] max-w-7xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        {{-- Banner Header --}}
        <div
            class="relative isolate overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#EA580C] via-[#C2410C] to-[#7C2405] p-7 text-white shadow-[0_18px_42px_-16px_rgba(184,56,9,0.45)] sm:p-10">
            {{-- Decorative ambient glow --}}
            <div class="pointer-events-none absolute -top-20 -right-16 -z-10 h-72 w-72 rounded-full bg-[#F3B825]/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 left-1/3 -z-10 h-60 w-60 rounded-full bg-[#FACC15]/15 blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FEF3C7] px-3 py-1 text-[11px] font-black tracking-wide text-[#92400E] uppercase">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#EA580C]"></span>
                        KATALOG LENGKAP
                    </span>
                    <span class="rounded-full border border-white/25 bg-white/10 px-3 py-1 text-[11px] font-extrabold text-white/95 backdrop-blur-md">
                        {{ $products->count() }} Pilihan Menu
                    </span>
                </div>

                <div class="mt-4 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-xl">
                        <h1 class="font-['Outfit'] text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                            Pilih Menu Favoritmu
                        </h1>
                        <p class="mt-2 text-xs leading-relaxed text-white/90 sm:text-sm">
                            Semua varian digoreng hangat dan renyah setelah pesananmu masuk. Tambahkan ke keranjang dan ambil tanpa antre di outlet.
                        </p>
                    </div>

                    {{-- Search Input Form --}}
                    <form method="get" action="{{ route('web.menu') }}" class="flex w-full max-w-md items-center gap-2">
                        @if ($category !== '')
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        <div class="relative flex-1">
                            <svg aria-hidden="true" class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-[#8C7E70]"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                            </svg>
                            <input name="q" value="{{ $search }}" placeholder="Cari bagian ayam atau camilan..."
                                aria-label="Cari menu di katalog"
                                class="h-12 w-full rounded-2xl border border-white/25 bg-white/95 pl-10 {{ $search !== '' ? 'pr-10' : 'pr-4' }} text-xs font-semibold text-[#1E1915] shadow-xs outline-none transition placeholder:text-[#8C7E70] focus:bg-white focus:ring-2 focus:ring-[#F3B825]">
                            @if ($search !== '')
                                <a href="{{ route('web.menu', array_filter(['category' => $category])) }}"
                                    aria-label="Hapus kata kunci pencarian"
                                    title="Hapus pencarian"
                                    class="absolute top-1/2 right-2.5 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-full bg-[#EFE4D2] text-[#6B5E50] transition hover:bg-[#E2D2BA] hover:text-[#1E1915]">
                                    <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        <button type="submit"
                            class="h-12 cursor-pointer rounded-2xl bg-[#F3B825] px-6 text-xs font-black text-[#1E1915] shadow-md transition hover:bg-[#FACC15] active:scale-95">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Category Filter Bar & Active Filter Status --}}
        <div class="mt-8 flex flex-col gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FFFDF9] px-4 py-3.5 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2" role="navigation" aria-label="Filter Kategori Menu">
                <a href="{{ route('web.menu', array_filter(['q' => $search])) }}"
                    @if ($category === '') aria-current="true" @endif
                    class="rounded-full px-4 py-2 text-xs font-black transition {{ $category === '' ? 'bg-[#B83809] text-white shadow-xs' : 'border border-[#EADFC9] bg-[#FAF4EA] text-[#6B5E50] hover:border-[#B83809] hover:text-[#B83809]' }}">
                    Semua Menu
                </a>
                @foreach ($categories as $itemCategory)
                    <a href="{{ route('web.menu', array_filter(['q' => $search, 'category' => $itemCategory])) }}"
                        @if ($category === $itemCategory) aria-current="true" @endif
                        class="rounded-full px-4 py-2 text-xs font-black transition {{ $category === $itemCategory ? 'bg-[#B83809] text-white shadow-xs' : 'border border-[#EADFC9] bg-[#FAF4EA] text-[#6B5E50] hover:border-[#B83809] hover:text-[#B83809]' }}">
                        {{ $itemCategory }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="font-bold text-[#7C6D5D]">
                    Menampilkan <strong class="text-[#1E1915]">{{ $products->count() }}</strong> menu
                    @if ($category !== '')
                        di kategori <strong class="text-[#B83809]">{{ $category }}</strong>
                    @endif
                    @if ($search !== '')
                        untuk "<strong class="text-[#B83809]">{{ $search }}</strong>"
                    @endif
                </span>
                @if ($search !== '' || $category !== '')
                    <a href="{{ route('web.menu') }}"
                        class="inline-flex items-center gap-1 rounded-full border border-[#DFD2BD] bg-white px-3 py-1 font-extrabold text-[#B83809] transition hover:border-[#B83809] hover:bg-[#FFF9F2]">
                        Reset Filter
                    </a>
                @endif
            </div>
        </div>

        {{-- Product Grid or Empty State --}}
        @if ($products->isEmpty())
            <div class="mt-10 rounded-[2.5rem] border border-dashed border-[#D8CBB8] bg-[#FFFDF9] px-6 py-20 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#FBF0C4] text-[#B83809]">
                    <svg aria-hidden="true" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                    </svg>
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
