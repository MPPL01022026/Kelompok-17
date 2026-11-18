@extends('layouts.web')
@section('title', 'Selesaikan Pesanan — Ayamo')

@section('content')
    <main id="main-content" tabindex="-1" data-rehydrate-cart="{{ $cartProducts->isEmpty() ? '1' : '0' }}" class="mx-auto min-h-[65vh] max-w-7xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('web.cart') }}" data-cart-sync-link class="inline-flex items-center gap-1.5 text-xs font-black text-[#B83809] hover:underline">
                <span>← Kembali ke Keranjang</span>
            </a>
            <span class="rounded-full border border-[#EBE1D0] bg-[#FFFDF9] px-3.5 py-1 text-[11px] font-extrabold text-[#7C6D5D]">
                Langkah 2 dari 2 · Konfirmasi Pesanan
            </span>
        </div>

        <div class="mt-4">
            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">LANGKAH TERAKHIR</span>
            <h1 class="mt-1 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">Selesaikan pesanan</h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Isi data pemesan dan tentukan cara pembayaran sebelum dikirim ke kasir outlet.</p>
        </div>

        @if ($cartProducts->isEmpty())
            <div class="mt-8 rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] px-6 py-20 text-center shadow-2xs">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#FBF0C4] text-[#B83809] shadow-2xs">
                    <svg aria-hidden="true" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                </div>
                <h2 class="mt-5 font-['Outfit'] text-2xl font-black text-[#1E1915]">Keranjang Belanja Kosong</h2>
                <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Pilih menu ayam crispy favoritmu terlebih dahulu sebelum melakukan checkout.</p>
                <a href="{{ route('web.menu') }}"
                    class="mt-6 inline-flex rounded-2xl bg-[#B83809] px-8 py-3.5 text-xs font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-95">
                    Jelajahi Katalog Menu
                </a>
            </div>
        @else
        <form id="web-checkout-form" method="post" action="{{ route('web.checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_420px]" novalidate>
            @csrf
            <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
            @foreach ($cart as $productId => $quantity)
                @if ($cartProducts->has($productId))
                    <input type="hidden" name="items[{{ $productId }}]" value="{{ $quantity }}">
                @endif
            @endforeach

            {{-- Form Sections --}}
            <div class="space-y-6">
                {{-- Card 1: Data Pemesan --}}
                <section class="rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
                    <div class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-[#F3B825] font-['Outfit'] text-sm font-black text-[#1E1915] shadow-2xs">
                            1
                        </span>
                        <div>
                            <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Data Pemesan</h2>
                            <p class="text-xs font-medium text-[#7C6D5D]">Digunakan untuk pemanggilan pesanan &amp; pelacakan riwayat.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#1E1915]">Nama Pemesan</span>
                            <input id="web_customer_name" name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" required minlength="2" maxlength="120"
                                placeholder="Contoh: Rifki"
                                class="h-12 w-full rounded-2xl border border-[#D8CBB8] bg-white px-4 text-xs font-semibold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:border-[#EA580C] focus:ring-2 focus:ring-[#EA580C]/20">
                            <x-input-error :messages="$errors->get('customer_name')" />
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#1E1915]">Nomor WhatsApp</span>
                            <input id="web_phone" name="phone" value="{{ old('phone') }}" type="tel" inputmode="tel" autocomplete="tel" required
                                placeholder="08xxxxxxxxxx"
                                class="h-12 w-full rounded-2xl border border-[#D8CBB8] bg-white px-4 text-xs font-semibold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:border-[#EA580C] focus:ring-2 focus:ring-[#EA580C]/20">
                            <x-input-error :messages="$errors->get('phone')" />
                        </label>
                    </div>

                    <label class="mt-5 block">
                        <span class="mb-2 block text-xs font-bold text-[#1E1915]">
                            Catatan Khusus <span class="font-normal text-[#8C7E70]">(opsional)</span>
                        </span>
                        <textarea name="note" rows="3" maxlength="500"
                            placeholder="Contoh: jangan terlalu pedas, ambil jam 19.30"
                            class="w-full rounded-2xl border border-[#D8CBB8] bg-white p-4 text-xs font-semibold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:border-[#EA580C] focus:ring-2 focus:ring-[#EA580C]/20">{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" />
                    </label>
                </section>

                {{-- Card 2: Metode Pembayaran --}}
                <section class="rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
                    <div class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-[#F3B825] font-['Outfit'] text-sm font-black text-[#1E1915] shadow-2xs">
                            2
                        </span>
                        <div>
                            <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Metode Pembayaran</h2>
                            <p class="text-xs font-medium text-[#7C6D5D]">Pilih bayar langsung di kasir atau non-tunai (Transfer &amp; QRIS).</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        {{-- Tunai --}}
                        <label class="group cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="peer sr-only" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                            <div class="relative flex h-full gap-4 rounded-2xl border-2 border-[#EBE1D0] bg-white p-5 transition hover:border-[#D8CBB8] peer-checked:border-[#B83809] peer-checked:bg-[#FFF9F2] peer-checked:shadow-xs peer-checked:[&_.pay-radio-dot]:border-[#B83809] peer-checked:[&_.pay-radio-dot]:bg-[#B83809] peer-checked:[&_.pay-radio-dot_span]:scale-100">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F0FDF4] text-[#166534]">
                                    <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1 pr-6">
                                    <b class="block font-['Outfit'] text-base font-black text-[#1E1915]">Bayar Tunai</b>
                                    <small class="mt-1 block text-xs leading-relaxed text-[#7C6D5D]">Bayar langsung di kasir saat mengambil pesanan.</small>
                                </div>
                                <span aria-hidden="true" class="pay-radio-dot absolute top-4 right-4 flex h-5 w-5 items-center justify-center rounded-full border-2 border-[#D8CBB8] transition">
                                    <span class="h-2 w-2 scale-0 rounded-full bg-white transition-transform"></span>
                                </span>
                            </div>
                        </label>

                        {{-- Transfer / QRIS --}}
                        <label class="group cursor-pointer">
                            <input type="radio" name="payment_method" value="whatsapp" class="peer sr-only" {{ old('payment_method') === 'whatsapp' ? 'checked' : '' }}>
                            <div class="relative flex h-full gap-4 rounded-2xl border-2 border-[#EBE1D0] bg-white p-5 transition hover:border-[#D8CBB8] peer-checked:border-[#B83809] peer-checked:bg-[#FFF9F2] peer-checked:shadow-xs peer-checked:[&_.pay-radio-dot]:border-[#B83809] peer-checked:[&_.pay-radio-dot]:bg-[#B83809] peer-checked:[&_.pay-radio-dot_span]:scale-100">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#FFF7ED] text-[#B83809]">
                                    <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M4 10V8l8-4 8 4v2M5 10v7m4-7v7m6-7v7m4-7v7M3 20h18" />
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1 pr-6">
                                    <b class="block font-['Outfit'] text-base font-black text-[#1E1915]">Transfer Bank / QRIS</b>
                                    <small class="mt-1 block text-xs leading-relaxed text-[#7C6D5D]">Pilih rekening transfer atau scan QRIS, lalu konfirmasi.</small>
                                </div>
                                <span aria-hidden="true" class="pay-radio-dot absolute top-4 right-4 flex h-5 w-5 items-center justify-center rounded-full border-2 border-[#D8CBB8] transition">
                                    <span class="h-2 w-2 scale-0 rounded-full bg-white transition-transform"></span>
                                </span>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" />
                </section>
            </div>

            {{-- Right Summary Aside --}}
            <aside class="h-fit rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-sm lg:sticky lg:top-28">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Ringkasan Pesanan</h2>
                    <span class="rounded-full bg-[#FEF3C7] px-3 py-1 text-[11px] font-black text-[#92400E]">
                        {{ $cartCount }} Porsi
                    </span>
                </div>

                <div class="mt-5 divide-y divide-[#EFE6D8]">
                    @foreach ($cart as $productId => $quantity)
                        @continue(! $cartProducts->has($productId))
                        @php $product = $cartProducts->get($productId); @endphp
                        <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] p-1.5">
                                <img src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="h-full w-full object-contain">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-black text-[#1E1915]">{{ $product->name }}</p>
                                <p class="text-[11px] font-semibold text-[#8C7E70]">{{ $quantity }} × Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-['Outfit'] text-xs font-black text-[#1E1915]">
                                Rp{{ number_format($product->price * $quantity, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex items-center justify-between rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] px-4 py-3.5">
                    <span class="font-['Outfit'] text-sm font-bold text-[#1E1915]">Total Bayar</span>
                    <strong class="font-['Outfit'] text-2xl font-black text-[#B83809]">
                        Rp{{ number_format($cartTotal, 0, ',', '.') }}
                    </strong>
                </div>

                @php $checkoutStoreStatus = \App\Models\Settings::getStoreStatus($settings ?? null); @endphp
                @if (! $checkoutStoreStatus['is_open'])
                    <div class="mt-4 rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-3.5 text-xs font-bold text-[#991B1B]" role="alert">
                        <p class="font-black uppercase">{{ $checkoutStoreStatus['label'] }}</p>
                        <p class="mt-1 font-medium">{{ $checkoutStoreStatus['reason'] }}</p>
                    </div>
                @endif
                <x-input-error :messages="$errors->get('store')" class="mt-2" />

                <button type="submit"
                    @disabled(! $checkoutStoreStatus['is_open'])
                    class="mt-5 flex h-14 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#F3B825] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95 disabled:cursor-not-allowed disabled:bg-[#E5D7C3] disabled:text-[#7C6D5D]">
                    <span>{{ $checkoutStoreStatus['is_open'] ? 'Buat Pesanan Sekarang' : 'Toko Sedang Tutup' }}</span>
                    @if ($checkoutStoreStatus['is_open'])
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    @endif
                </button>

                <p class="mt-3 text-center text-[11px] leading-relaxed text-[#8C7E70]">
                    Harga final tanpa biaya tambahan. Pesanan langsung diteruskan ke kasir Ayamo.
                </p>
            </aside>
        </form>

        <script>
            (function() {
                const nameInput = document.getElementById('web_customer_name');
                const phoneInput = document.getElementById('web_phone');
                const form = document.getElementById('web-checkout-form');

                try {
                    if (nameInput && !nameInput.value && localStorage.getItem('ayamo_customer_name')) {
                        nameInput.value = localStorage.getItem('ayamo_customer_name');
                    }
                    if (phoneInput && !phoneInput.value && localStorage.getItem('ayamo_customer_phone')) {
                        phoneInput.value = localStorage.getItem('ayamo_customer_phone');
                    }
                } catch (e) {}

                form?.addEventListener('submit', () => {
                    try {
                        const n = (nameInput?.value || '').trim();
                        const p = (phoneInput?.value || '').trim();
                        if (n.length >= 2) localStorage.setItem('ayamo_customer_name', n);
                        if (p.length >= 8) localStorage.setItem('ayamo_customer_phone', p);
                    } catch (e) {}
                });
            })();
        </script>
        @endif
    </main>
@endsection
