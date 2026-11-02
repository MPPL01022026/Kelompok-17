@extends('layouts.web')
@section('title', 'Keranjang Belanja — Ayamo')

@section('content')
    <main class="mx-auto min-h-[65vh] max-w-7xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <div>
            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">PESANAN KAMU</span>
            <h1 class="mt-1 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">Keranjang Belanja</h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Atur jumlah menu pilihanmu sebelum melanjutkan ke checkout.</p>
        </div>

        @if ($cartProducts->isEmpty())
            <div class="mt-10 rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] px-6 py-20 text-center shadow-2xs">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#FBF0C4] text-4xl shadow-2xs">
                    🍗
                </div>
                <h2 class="mt-5 font-['Outfit'] text-2xl font-black text-[#1E1915]">Keranjangmu Masih Kosong</h2>
                <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Yuk pilih potongan ayam crispy favoritmu dulu!</p>
                <a href="{{ route('web.menu') }}"
                    class="mt-6 inline-flex rounded-2xl bg-[#B83809] px-8 py-3.5 text-xs font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-95">
                    Jelajahi Menu
                </a>
            </div>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_380px]">
                {{-- Left Item List --}}
                <form method="post" action="{{ route('web.cart.update') }}" data-cart-update-form class="space-y-4">
                    @csrf
                    @foreach ($cart as $productId => $quantity)
                        @continue(! $cartProducts->has($productId))
                        @php($product = $cartProducts->get($productId))
                        <article class="grid grid-cols-[88px_1fr] items-center gap-4 rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs transition hover:border-[#EA580C]/40 sm:grid-cols-[100px_1fr_auto] sm:gap-5 sm:p-5">
                            {{-- Image --}}
                            <div class="flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] p-2">
                                <img src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-contain">
                            </div>

                            {{-- Title & Category --}}
                            <div>
                                <span class="text-[10px] font-black tracking-wider text-[#EA580C] uppercase">{{ $product->category }}</span>
                                <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">{{ $product->name }}</h2>
                                <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">Rp{{ number_format($product->price, 0, ',', '.') }} / porsi</p>
                            </div>

                            {{-- Qty Stepper & Subtotal --}}
                            <div class="col-span-2 flex items-center justify-between gap-4 sm:col-span-1 sm:flex-col sm:items-end">
                                <label class="flex items-center rounded-2xl border border-[#D8CBB8] bg-white p-1">
                                    <span class="sr-only">Jumlah {{ $product->name }}</span>
                                    <input name="quantities[{{ $productId }}]" type="number" min="0" max="{{ $product->stock }}"
                                        value="{{ $quantity }}"
                                        class="h-9 w-16 bg-transparent text-center text-xs font-black text-[#1E1915] outline-none">
                                </label>
                                <strong class="font-['Outfit'] text-base font-black text-[#B83809]">
                                    Rp{{ number_format($product->price * $quantity, 0, ',', '.') }}
                                </strong>
                            </div>
                        </article>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit"
                            class="cursor-pointer rounded-2xl border border-[#D8CBB8] bg-white px-5 py-2.5 text-xs font-black text-[#1E1915] shadow-2xs transition hover:border-[#EA580C] hover:text-[#EA580C] active:scale-95">
                            Perbarui Keranjang
                        </button>
                    </div>
                </form>

                {{-- Right Summary Aside --}}
                <aside class="h-fit rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-sm lg:sticky lg:top-28">
                    <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Ringkasan Pesanan</h2>

                    <div class="mt-6 space-y-3 text-xs font-semibold text-[#7C6D5D]">
                        <div class="flex justify-between">
                            <span>Jumlah Menu</span>
                            <span class="font-bold text-[#1E1915]">{{ $cartCount }} Item</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-[#1E1915]">Rp{{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="my-5 border-t border-dashed border-[#D8CBB8]"></div>

                    <div class="flex items-end justify-between">
                        <span class="font-['Outfit'] text-sm font-bold text-[#1E1915]">Total Pembayaran</span>
                        <strong class="font-['Outfit'] text-2xl font-black text-[#B83809]">
                            Rp{{ number_format($cartTotal, 0, ',', '.') }}
                        </strong>
                    </div>

                    <p class="mt-3 text-[11px] leading-relaxed text-[#8C7E70]">
                        Harga final tanpa biaya tambahan. Pembayaran dapat dilakukan tunai di kasir atau transfer bank/QRIS.
                    </p>

                    <a href="{{ route('web.checkout') }}"
                        class="mt-6 flex h-13 items-center justify-center rounded-2xl bg-[#F3B825] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95">
                        Lanjut ke Checkout →
                    </a>

                    <a href="{{ route('web.menu') }}"
                        class="mt-3 block text-center text-xs font-bold text-[#8C7E70] transition hover:text-[#EA580C]">
                        + Tambah Menu Lain
                    </a>
                </aside>
            </div>
        @endif
    </main>
@endsection
