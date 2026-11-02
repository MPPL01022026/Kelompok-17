@extends('layouts.web')
@section('title', 'Riwayat Pesanan — Ayamo')

@section('content')
    <main class="mx-auto min-h-[65vh] max-w-5xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <div>
            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">TERSIMPAN DI PERANGKAT INI</span>
            <h1 class="mt-1 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">Riwayat Pesanan</h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Pantau status atau pesan ulang menu favoritmu dengan cepat.</p>
        </div>

        @if ($orders->isEmpty())
            <div class="mt-8 rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] px-6 py-20 text-center shadow-2xs">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#FBF0C4] text-4xl shadow-2xs">
                    🧾
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
                        $isConfirmed = in_array($order->status, ['confirmed', 'completed'], true);
                    @endphp
                    <article class="grid gap-5 rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#EA580C]/40 hover:shadow-md sm:p-6 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] text-2xl shadow-2xs">
                                🍗
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h2 class="font-['Outfit'] text-xl font-black text-[#1E1915]">{{ $order->code }}</h2>
                                    <span class="rounded-full px-3 py-0.5 text-[11px] font-black shadow-2xs {{ $isConfirmed ? 'bg-[#DCFCE7] text-[#166534]' : 'bg-[#FEF3C7] text-[#92400E]' }}">
                                        {{ $isConfirmed ? '✓ Dikonfirmasi' : '⏳ Menunggu Konfirmasi' }}
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
