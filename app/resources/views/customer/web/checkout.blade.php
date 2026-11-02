@extends('layouts.web')
@section('title', 'Selesaikan Pesanan — Ayamo')

@section('content')
    <main class="mx-auto min-h-[65vh] max-w-7xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <a href="{{ route('web.cart') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-[#B83809] hover:underline">
            <span>← Kembali ke Keranjang</span>
        </a>

        <div class="mt-4">
            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">LANGKAH TERAKHIR</span>
            <h1 class="mt-1 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">Selesaikan Pesanan</h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Isi data pemesan dan tentukan cara pembayaran.</p>
        </div>

        <form method="post" action="{{ route('web.checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_420px]" novalidate>
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
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-[#F3B825] font-['Outfit'] text-sm font-black text-[#1E1915]">
                            1
                        </span>
                        <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Data Pemesan</h2>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#1E1915]">Nama Pemesan</span>
                            <input name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" required minlength="2" maxlength="120"
                                placeholder="Nama kamu"
                                class="h-12 w-full rounded-2xl border border-[#D8CBB8] bg-white px-4 text-xs font-semibold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:border-[#EA580C] focus:ring-2 focus:ring-[#EA580C]/20">
                            <x-input-error :messages="$errors->get('customer_name')" />
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#1E1915]">Nomor WhatsApp</span>
                            <input name="phone" value="{{ old('phone') }}" type="tel" inputmode="tel" autocomplete="tel" required
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
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-[#F3B825] font-['Outfit'] text-sm font-black text-[#1E1915]">
                            2
                        </span>
                        <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Metode Pembayaran</h2>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        {{-- Tunai --}}
                        <label class="group cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="peer sr-only" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                            <div class="flex h-full gap-4 rounded-2xl border-2 border-[#EBE1D0] bg-white p-5 transition peer-checked:border-[#B83809] peer-checked:bg-[#FFF9F2] peer-checked:shadow-xs">
                                <span class="text-3xl">💵</span>
                                <div>
                                    <b class="block font-['Outfit'] text-base font-black text-[#1E1915]">Bayar Tunai</b>
                                    <small class="mt-1 block text-xs leading-relaxed text-[#7C6D5D]">Bayar langsung di kasir saat mengambil pesanan.</small>
                                </div>
                            </div>
                        </label>

                        {{-- Transfer --}}
                        <label class="group cursor-pointer">
                            <input type="radio" name="payment_method" value="whatsapp" class="peer sr-only" {{ old('payment_method') === 'whatsapp' ? 'checked' : '' }}>
                            <div class="flex h-full gap-4 rounded-2xl border-2 border-[#EBE1D0] bg-white p-5 transition peer-checked:border-[#B83809] peer-checked:bg-[#FFF9F2] peer-checked:shadow-xs">
                                <span class="text-3xl">🏦</span>
                                <div>
                                    <b class="block font-['Outfit'] text-base font-black text-[#1E1915]">Transfer Bank / QRIS</b>
                                    <small class="mt-1 block text-xs leading-relaxed text-[#7C6D5D]">Transfer online, lalu konfirmasi via WhatsApp.</small>
                                </div>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" />
                </section>
            </div>

            {{-- Right Summary Aside --}}
            <aside class="h-fit rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-sm lg:sticky lg:top-28">
                <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">Ringkasan Pesanan</h2>

                <div class="mt-6 space-y-3.5">
                    @foreach ($cart as $productId => $quantity)
                        @continue(! $cartProducts->has($productId))
                        @php($product = $cartProducts->get($productId))
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] p-1.5">
                                <img src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}" alt="" class="h-full w-full object-contain">
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

                <div class="my-6 border-t border-dashed border-[#D8CBB8]"></div>

                <div class="flex items-end justify-between">
                    <span class="font-['Outfit'] text-sm font-bold text-[#1E1915]">Total Bayar</span>
                    <strong class="font-['Outfit'] text-2xl font-black text-[#B83809]">
                        Rp{{ number_format($cartTotal, 0, ',', '.') }}
                    </strong>
                </div>

                <button type="submit"
                    class="mt-6 flex h-14 w-full cursor-pointer items-center justify-center rounded-2xl bg-[#F3B825] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95">
                    Buat Pesanan Sekarang →
                </button>

                <p class="mt-3 text-center text-[11px] leading-relaxed text-[#8C7E70]">
                    Pesanan akan langsung diteruskan ke sistem kasir Ayamo.
                </p>
            </aside>
        </form>
    </main>
@endsection
