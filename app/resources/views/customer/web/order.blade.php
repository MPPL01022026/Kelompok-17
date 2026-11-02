@extends('layouts.web')
@section('title', 'Status Pesanan ' . $order->code . ' — Ayamo')

@section('content')
    @php
        $isConfirmed = in_array($order->status, ['confirmed', 'completed'], true);
    @endphp

    <main class="mx-auto min-h-[65vh] max-w-4xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <a href="{{ route('web.history') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-[#B83809] hover:underline">
            <span>← Kembali ke Riwayat Pesanan</span>
        </a>

        <section class="mt-5 overflow-hidden rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] shadow-sm">
            {{-- Header Band --}}
            <div class="bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <span class="text-xs font-bold text-white/75">Kode Pesanan</span>
                        <h1 class="mt-0.5 font-['Outfit'] text-3xl font-black text-[#FEF08A] sm:text-4xl">
                            {{ $order->code }}
                        </h1>
                    </div>
                    <span class="w-fit rounded-full px-4 py-1.5 text-xs font-black shadow-2xs {{ $isConfirmed ? 'bg-[#DCFCE7] text-[#166534]' : 'bg-[#FEF3C7] text-[#92400E]' }}">
                        {{ $isConfirmed ? '✓ Pesanan Dikonfirmasi' : '⏳ Menunggu Konfirmasi' }}
                    </span>
                </div>
            </div>

            {{-- Body Grid --}}
            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1fr_280px]">
                {{-- Left: Item Breakdown --}}
                <div>
                    <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Rincian Menu</h2>
                    <div class="mt-4 divide-y divide-[#EFE6D8]">
                        @foreach ($order->parsed_items as $item)
                            <div class="flex items-center justify-between gap-4 py-3.5">
                                <div>
                                    <p class="text-xs font-black text-[#1E1915]">{{ $item['name'] ?? 'Menu Ayamo' }}</p>
                                    <p class="text-[11px] font-semibold text-[#8C7E70]">
                                        {{ $item['quantity'] ?? 1 }} × Rp{{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                <strong class="font-['Outfit'] text-sm font-black text-[#1E1915]">
                                    Rp{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}
                                </strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t-2 border-[#1E1915] pt-4">
                        <span class="font-['Outfit'] text-sm font-bold text-[#1E1915]">Total Pembayaran</span>
                        <strong class="font-['Outfit'] text-2xl font-black text-[#B83809]">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                {{-- Right: Customer Data & Action --}}
                <aside class="rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] p-5">
                    <h2 class="font-['Outfit'] text-sm font-black text-[#1E1915]">Data Pemesan</h2>
                    <dl class="mt-3.5 space-y-3 text-xs">
                        <div>
                            <dt class="font-semibold text-[#8C7E70]">Nama</dt>
                            <dd class="font-black text-[#1E1915]">{{ $order->customer_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#8C7E70]">WhatsApp</dt>
                            <dd class="font-black text-[#1E1915]">{{ $order->phone }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#8C7E70]">Metode Pembayaran</dt>
                            <dd class="font-black text-[#1E1915]">{{ in_array($order->payment_method, ['cod', 'tunai']) ? 'Tunai di Kasir' : 'Transfer Bank / QRIS' }}</dd>
                        </div>
                        @if ($order->note)
                            <div>
                                <dt class="font-semibold text-[#8C7E70]">Catatan</dt>
                                <dd class="leading-relaxed text-[#1E1915]">{{ $order->note }}</dd>
                            </div>
                        @endif
                    </dl>

                    <form method="post" action="{{ route('web.orders.reorder', $order->id) }}" class="mt-6">
                        @csrf
                        <button type="submit"
                            class="flex h-11 w-full cursor-pointer items-center justify-center rounded-2xl bg-[#F3B825] text-xs font-black text-[#1E1915] shadow-2xs transition hover:bg-[#FACC15] active:scale-95">
                            Pesan Menu Ini Lagi
                        </button>
                    </form>
                </aside>
            </div>
        </section>
    </main>
@endsection
