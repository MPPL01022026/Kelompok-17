@extends('layouts.web')
@section('title', 'Riwayat Pesanan — Ayamo')

@section('content')
    <main id="main-content" tabindex="-1" class="mx-auto min-h-[65vh] max-w-5xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <div>
            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">TERSIMPAN DI PERANGKAT INI</span>
            <h1 class="mt-1 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">Riwayat Pesanan</h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Pantau status atau pesan ulang menu favoritmu dengan cepat.</p>
        </div>

        {{-- Lacak Pesanan dari Perangkat Lain --}}
        <form method="get" action="{{ route('web.history') }}"
            class="mt-6 grid gap-3 rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:p-5">
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-black tracking-wide text-[#4A3B31] uppercase">Lacak Kode Pesanan</span>
                <input name="code" value="{{ request('code') }}" placeholder="Contoh: AYM-1234"
                    class="h-11 w-full rounded-xl border border-[#D8CBB8] bg-white px-3.5 text-xs font-bold text-[#1E1915] uppercase outline-none transition placeholder:normal-case placeholder:text-[#8C7E70] focus:border-[#EA580C]">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-black tracking-wide text-[#4A3B31] uppercase">Nomor WhatsApp Pemesan</span>
                <input name="phone" value="{{ request('phone') }}" placeholder="08xxxxxxxxxx" type="tel"
                    class="h-11 w-full rounded-xl border border-[#D8CBB8] bg-white px-3.5 text-xs font-bold text-[#1E1915] outline-none transition placeholder:text-[#8C7E70] focus:border-[#EA580C]">
            </label>
            <button type="submit"
                class="h-11 cursor-pointer rounded-xl bg-[#B83809] px-6 text-xs font-black text-white shadow-2xs transition hover:bg-[#992D06] active:scale-95">
                Lacak Pesanan
            </button>
        </form>

        @if ($orders->isEmpty())
            <div class="mt-8 rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] px-6 py-20 text-center shadow-2xs">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#FBF0C4] text-[#B83809] shadow-2xs">
                    <svg aria-hidden="true" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>
                </div>
                <h2 class="mt-5 font-['Outfit'] text-2xl font-black text-[#1E1915]">Belum Ada Riwayat Pesanan</h2>
                <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Pesanan yang dibuat dari perangkat ini akan muncul di sini.</p>
                <a href="{{ route('web.menu') }}"
                    class="mt-6 inline-flex rounded-2xl bg-[#B83809] px-8 py-3.5 text-xs font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-95">
                    Pesan Sekarang
                </a>
            </div>
        @else
            <div class="mt-8 space-y-4">
                @foreach ($orders as $order)
                    @php
                        $statusBadge = match ($order->status) {
                            'processing' => ['label' => 'Diproses', 'class' => 'bg-[#FFEDD5] text-[#9A3412]'],
                            'ready' => ['label' => 'Siap Diambil', 'class' => 'bg-[#E0F2FE] text-[#075985]'],
                            'confirmed', 'completed' => ['label' => 'Dikonfirmasi', 'class' => 'bg-[#DCFCE7] text-[#166534]'],
                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-[#FEE2E2] text-[#991B1B]'],
                            default => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-[#FEF3C7] text-[#92400E]'],
                        };
                    @endphp
                    <article class="grid gap-5 rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#EA580C]/40 hover:shadow-md sm:p-6 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] text-[#B83809] shadow-2xs">
                                <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">{{ $order->code }}</h2>
                                    <span class="rounded-full px-3 py-0.5 text-[11px] font-black shadow-2xs {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-[#7C6D5D]">{{ $order->items_summary }}</p>
                                <p class="mt-1 font-['Outfit'] text-base font-black text-[#B83809]">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('web.orders.show', $order->id) }}"
                                class="rounded-2xl border border-[#D8CBB8] bg-white px-5 py-2.5 text-xs font-black text-[#1E1915] shadow-2xs transition hover:border-[#EA580C] hover:text-[#EA580C] active:scale-95">
                                Detail
                            </a>
                            <form method="post" action="{{ route('web.orders.reorder', $order->id) }}">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer rounded-2xl bg-[#F3B825] px-5 py-2.5 text-xs font-black text-[#1E1915] shadow-2xs transition hover:bg-[#FACC15] active:scale-95">
                                    Pesan Lagi
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>
@endsection
