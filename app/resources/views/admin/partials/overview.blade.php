@php
    $periodLabels = [
        'day' => 'Harian',
        'week' => 'Mingguan',
        'month' => 'Bulanan',
    ];
    $maxBestSellerUnits = max(1, (int) collect($stats['best_sellers'])->max('units'));
    $firstProduct = $products->first(fn ($p) => $p->stock > 0) ?? $products->first();
@endphp

<div class="space-y-6">
    {{-- Workspace Hero Header --}}
    <section class="relative overflow-hidden rounded-[28px] border border-[#EBE1D0] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white shadow-sm sm:p-8">
        <div class="pointer-events-none absolute -top-16 -right-12 h-48 w-48 rounded-full bg-[#F59E0B]/25 blur-2xl"></div>
        <div class="relative z-10 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-black tracking-wide text-[#FEF08A] uppercase">
                    Dashboard Operasional
                </span>
                <h1 class="mt-2.5 font-['Outfit'] text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Ringkasan toko
                </h1>
                <p class="mt-1 text-sm font-medium text-white/85">
                    Pantau omzet, stok kritis, menu terlaris, dan input kasir manual.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex rounded-2xl bg-black/20 p-1 backdrop-blur-xs" role="group" aria-label="Filter periode grafik">
                    @foreach ($periodLabels as $key => $label)
                        @php $isCurrent = $period === $key; @endphp
                        <a
                            href="/admin?tab=overview&period={{ $key }}"
                            class="rounded-xl px-3.5 py-2 text-xs font-black transition {{ $isCurrent ? 'bg-[#F59E0B] text-[#1E1915] shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <a href="/admin?tab=reports" class="inline-flex items-center gap-1.5 rounded-2xl border border-white/25 bg-white/10 px-4 py-2.5 text-xs font-black text-white backdrop-blur-xs transition hover:bg-white/20">
                    <span>Laporan UMKM</span>
                </a>
                <a href="/admin?tab=orders" class="inline-flex items-center gap-1.5 rounded-2xl bg-white px-4 py-2.5 text-xs font-black text-[#B83809] shadow-xs transition hover:bg-[#FEF3C7]">
                    <span>Kelola Pesanan</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- 4 KPI Cards --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#F59E0B]/60">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-black tracking-wide text-[#7C6D5D] uppercase">Total Omzet</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#FEF3C7] text-[#B83809]">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <p class="mt-3 font-['Outfit'] text-2xl font-black tracking-tight text-[#1E1915] sm:text-3xl">
                Rp{{ number_format($stats['revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $stats['transactions'] }} transaksi · Rata-rata Rp{{ number_format($stats['avg_order_value'], 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#22C55E]/60">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-black tracking-wide text-[#7C6D5D] uppercase">Penjualan hari ini</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#DCFCE7] text-[#166534]">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                </span>
            </div>
            <p class="mt-3 font-['Outfit'] text-2xl font-black tracking-tight text-[#166534] sm:text-3xl">
                Rp{{ number_format($stats['today_revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $stats['today_transactions'] }} transaksi hari ini · {{ $stats['units'] }} porsi total
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#EA580C]/60">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-black tracking-wide text-[#7C6D5D] uppercase">Pesanan Menunggu</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#FFEDD5] text-[#9A3412]">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>
                </span>
            </div>
            <p class="mt-3 font-['Outfit'] text-2xl font-black tracking-tight text-[#B83809] sm:text-3xl">
                {{ $stats['pending_orders'] }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $stats['processing_orders'] }} diproses/siap · {{ $stats['completed_orders'] }} selesai
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#DC2626]/50">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-black tracking-wide text-[#7C6D5D] uppercase">Stok Perlu Perhatian</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#FEE2E2] text-[#991B1B]">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                </span>
            </div>
            <p class="mt-3 font-['Outfit'] text-2xl font-black tracking-tight {{ $stats['low_stock'] > 0 ? 'text-[#991B1B]' : 'text-[#1E1915]' }} sm:text-3xl">
                {{ $stats['low_stock'] }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $stats['out_of_stock'] }} habis · {{ $stats['active_products'] }} menu aktif
            </p>
        </article>
    </section>

    {{-- Main Asymmetric Grid --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(350px,1fr)] xl:items-start">
        {{-- Left Column: Chart + Best Sellers + Recent Transactions --}}
        <div class="space-y-6">
            {{-- Revenue Chart --}}
            <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">
                            Tren Penjualan ({{ $periodLabels[$period] ?? 'Harian' }})
                        </h2>
                        <p class="text-xs font-semibold text-[#7C6D5D]">
                            Rekap transaksi terkonfirmasi dan penjualan kasir manual.
                        </p>
                    </div>
                    <span class="rounded-full bg-[#FAF4EA] px-3 py-1 text-xs font-black text-[#B83809]">
                        Maks: Rp{{ number_format($stats['chart_max'], 0, ',', '.') }}
                    </span>
                </div>

                @if ($stats['chart']->isEmpty())
                    <div class="mt-6 rounded-2xl border border-dashed border-[#DFD2BD] bg-[#FAF4EA]/60 p-8 text-center">
                        <p class="font-['Outfit'] text-sm font-black text-[#4A3B31]">Belum ada data transaksi</p>
                        <p class="mt-1 text-xs font-semibold text-[#7C6D5D]">
                            Konfirmasi pesanan masuk atau catat transaksi manual di sebelah kanan untuk melihat grafik.
                        </p>
                    </div>
                @else
                    {{-- Column Bars --}}
                    <div class="mt-6 flex items-end gap-2.5 overflow-x-auto rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/50 px-4 pt-6 pb-4">
                        @foreach ($stats['chart'] as $point)
                            @php
                                $pct = max(8, (int) round(($point['revenue'] / $stats['chart_max']) * 100));
                            @endphp
                            <div class="group flex min-w-[54px] flex-1 flex-col items-center gap-2">
                                <span class="text-[10px] font-black text-[#7C6D5D] transition group-hover:text-[#B83809]">
                                    Rp{{ number_format($point['revenue'], 0, ',', '.') }}
                                </span>
                                <div class="flex h-36 w-full max-w-[42px] items-end rounded-xl bg-[#EFE4D2] p-1">
                                    <div
                                        class="w-full rounded-lg bg-gradient-to-t from-[#B83809] to-[#F59E0B] transition-all group-hover:brightness-105"
                                        style="height: {{ $pct }}%"
                                        title="{{ $point['label'] }}: Rp{{ number_format($point['revenue'], 0, ',', '.') }} ({{ $point['transactions'] }} trx)"
                                    ></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-[11px] font-black text-[#1E1915]">{{ $point['label'] }}</p>
                                    <p class="text-[10px] font-semibold text-[#7C6D5D]">{{ $point['transactions'] }} trx</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Best Sellers & Recent Transactions --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Menu Terlaris --}}
                <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Menu Terlaris</h2>
                        <a href="/admin?tab=products" class="text-xs font-black text-[#B83809] hover:underline">
                            Katalog →
                        </a>
                    </div>

                    @if (empty($stats['best_sellers']))
                        <p class="mt-4 rounded-2xl bg-[#FAF4EA] p-4 text-xs font-semibold text-[#7C6D5D]">
                            Belum ada item terjual yang tercatat.
                        </p>
                    @else
                        <div class="mt-4 space-y-3.5">
                            @foreach ($stats['best_sellers'] as $index => $best)
                                @php
                                    $barWidth = max(10, (int) round(($best['units'] / $maxBestSellerUnits) * 100));
                                @endphp
                                <div class="rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/50 p-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#FEF3C7] text-[11px] font-black text-[#92400E]">
                                            {{ $index + 1 }}
                                        </span>
                                        <img
                                            src="{{ asset(ltrim((string) $best['image_url'], '/')) }}"
                                            alt="{{ $best['name'] }}"
                                            loading="lazy"
                                            decoding="async"
                                            class="h-10 w-10 shrink-0 rounded-xl border border-[#EBE1D0] bg-white object-contain p-1"
                                        >
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="truncate text-sm font-black text-[#1E1915]">{{ $best['name'] }}</p>
                                                <span class="shrink-0 text-xs font-black text-[#B83809]">{{ $best['units'] }} porsi</span>
                                            </div>
                                            <div class="mt-1 flex items-center justify-between gap-2 text-[11px] font-semibold text-[#7C6D5D]">
                                                <span>{{ $best['category'] }}</span>
                                                <span>Rp{{ number_format($best['revenue'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-[#EBE1D0]">
                                        <div class="h-full rounded-full bg-gradient-to-r from-[#B83809] to-[#F59E0B]" style="width: {{ $barWidth }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Transaksi Terbaru --}}
                <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Transaksi Terbaru</h2>
                        <span class="text-xs font-bold text-[#7C6D5D]">{{ $recentTransactions->count() }} terakhir</span>
                    </div>

                    @if ($recentTransactions->isEmpty())
                        <p class="mt-4 rounded-2xl bg-[#FAF4EA] p-4 text-xs font-semibold text-[#7C6D5D]">
                            Belum ada transaksi masuk.
                        </p>
                    @else
                        <div class="mt-4 space-y-2.5">
                            @foreach ($recentTransactions as $tx)
                                @php
                                    $txItems = json_decode((string) $tx->items, true) ?: [];
                                    $txSummary = collect($txItems)->map(fn ($it) => ($it['quantity'] ?? 1) . '× ' . ($it['name'] ?? 'Item'))->implode(', ');
                                    $isManual = $tx->source === 'manual';
                                @endphp
                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/45 px-3.5 py-2.5">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $isManual ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-[#DCFCE7] text-[#166534]' }}">
                                                {{ $isManual ? 'Kasir' : 'Online' }}
                                            </span>
                                            <p class="truncate text-xs font-black text-[#1E1915]">{{ $tx->customer_name }}</p>
                                        </div>
                                        <p class="mt-0.5 truncate text-[11px] font-semibold text-[#7C6D5D]">
                                            {{ $txSummary ?: 'Pesanan Ayamo' }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="font-['Outfit'] text-xs font-black text-[#B83809]">
                                            Rp{{ number_format($tx->total, 0, ',', '.') }}
                                        </p>
                                        <p class="text-[10px] font-semibold text-[#7C6D5D]">
                                            {{ $tx->created_at ? \Illuminate\Support\Carbon::parse($tx->created_at)->format('H:i') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>

        {{-- Right Column: Quick POS + Latest Orders --}}
        <div class="space-y-6">
            {{-- Quick POS Form --}}
            <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="rounded-full bg-[#FEF3C7] px-2.5 py-0.5 text-[10px] font-black tracking-wide text-[#92400E] uppercase">
                            Kasir Cepat (POS)
                        </span>
                        <h2 class="mt-1.5 font-['Outfit'] text-lg font-black text-[#1E1915]">
                            Catat transaksi manual
                        </h2>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FAF4EA] text-[#B83809]">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>
                    </span>
                </div>

                <form action="/admin/transactions" method="post" class="mt-5 space-y-4" id="pos-manual-form">
                    @csrf

                    <div>
                        <label for="pos-customer" class="block text-xs font-black text-[#4A3B31]">Nama Pelanggan</label>
                        <input
                            id="pos-customer"
                            name="customer_name"
                            type="text"
                            required
                            value="{{ old('customer_name') }}"
                            placeholder="Contoh: Pembeli Walk-in"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('customer_name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="pos-product" class="block text-xs font-black text-[#4A3B31]">Pilih Menu</label>
                        <select
                            id="pos-product"
                            name="product_id"
                            required
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                            @foreach ($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    data-stock="{{ $product->stock }}"
                                    @selected(old('product_id', $firstProduct?->id) == $product->id)
                                    @disabled($product->stock <= 0)
                                >
                                    {{ $product->name }} — Rp{{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('product_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="pos-qty" class="block text-xs font-black text-[#4A3B31]">Jumlah Porsi</label>
                            <input
                                id="pos-qty"
                                name="quantity"
                                type="number"
                                min="1"
                                max="500"
                                required
                                value="{{ old('quantity', 1) }}"
                                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-black text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                            >
                            <div class="mt-2 flex gap-1.5">
                                @foreach ([1, 2, 3, 5] as $preset)
                                    <button
                                        type="button"
                                        data-pos-qty="{{ $preset }}"
                                        class="rounded-lg border border-[#EBE1D0] bg-[#FAF4EA] px-2.5 py-1 text-[11px] font-black text-[#4A3B31] transition hover:border-[#B83809] hover:text-[#B83809]"
                                    >
                                        {{ $preset }}×
                                    </button>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                        </div>

                        <div>
                            <label for="pos-cash" class="block text-xs font-black text-[#4A3B31]">Uang Diterima (Opsional)</label>
                            <input
                                id="pos-cash"
                                type="number"
                                min="0"
                                step="1000"
                                placeholder="Contoh: 20000"
                                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
                            >
                            <p id="pos-change" class="mt-2 text-[11px] font-extrabold text-[#7C6D5D]">
                                Hitung kembalian otomatis
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA] px-4 py-3">
                        <span class="text-xs font-extrabold text-[#7C6D5D]">Estimasi Total</span>
                        <span id="pos-subtotal" class="font-['Outfit'] text-lg font-black text-[#B83809]">
                            Rp{{ number_format(($firstProduct?->price ?? 0) * (int) old('quantity', 1), 0, ',', '.') }}
                        </span>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#F59E0B] px-4 py-3 font-['Outfit'] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#D97706] active:scale-[0.99]"
                    >
                        <span>Catat Transaksi Kasir</span>
                        <span>→</span>
                    </button>
                </form>
            </section>

            {{-- Antrean Pesanan Terbaru --}}
            <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Antrean Pesanan</h2>
                    <a href="/admin?tab=orders" class="text-xs font-black text-[#B83809] hover:underline">
                        Semua ({{ $orders->count() }}) →
                    </a>
                </div>

                @if ($orders->isEmpty())
                    <p class="mt-4 rounded-2xl bg-[#FAF4EA] p-4 text-xs font-semibold text-[#7C6D5D]">
                        Belum ada pesanan pelanggan masuk.
                    </p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($orders->take(5) as $order)
                            @php
                                $badge = match ($order->status) {
                                    'processing' => ['label' => 'Diproses', 'class' => 'bg-[#FFEDD5] text-[#9A3412]'],
                                    'ready' => ['label' => 'Siap Diambil', 'class' => 'bg-[#E0F2FE] text-[#075985]'],
                                    'confirmed', 'completed' => ['label' => 'Selesai', 'class' => 'bg-[#DCFCE7] text-[#166534]'],
                                    'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-[#FEE2E2] text-[#991B1B]'],
                                    default => ['label' => 'Menunggu', 'class' => 'bg-[#FEF3C7] text-[#92400E]'],
                                };
                                $canQuickConfirm = in_array($order->status, ['pending', 'processing', 'ready'], true);
                            @endphp
                            <div class="rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/55 p-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-['Outfit'] text-sm font-black text-[#1E1915]">{{ $order->code }}</span>
                                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black {{ $badge['class'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>
                                        <p class="mt-0.5 truncate text-xs font-bold text-[#4A3B31]">{{ $order->customer_name }}</p>
                                        <p class="mt-0.5 truncate text-[11px] font-medium text-[#7C6D5D]">{{ $order->items_summary }}</p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="font-['Outfit'] text-sm font-black text-[#B83809]">
                                            Rp{{ number_format($order->total, 0, ',', '.') }}
                                        </p>
                                        @if ($canQuickConfirm)
                                            <form action="/admin/orders/{{ $order->id }}/confirm" method="post" class="mt-1.5">
                                                @csrf
                                                <button type="submit" class="rounded-xl bg-[#B83809] px-2.5 py-1 text-[11px] font-black text-white transition hover:bg-[#992D06]">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    const productSelect = document.getElementById('pos-product');
    const qtyInput = document.getElementById('pos-qty');
    const cashInput = document.getElementById('pos-cash');
    const subtotalEl = document.getElementById('pos-subtotal');
    const changeEl = document.getElementById('pos-change');
    if (!productSelect || !qtyInput || !subtotalEl) return;

    const formatRp = (num) => 'Rp' + Number(num || 0).toLocaleString('id-ID');

    const updateTotals = () => {
        const option = productSelect.options[productSelect.selectedIndex];
        const price = option ? Number(option.dataset.price || 0) : 0;
        const qty = Math.max(1, Number(qtyInput.value || 1));
        const total = price * qty;
        subtotalEl.textContent = formatRp(total);

        if (cashInput && changeEl) {
            const cash = Number(cashInput.value || 0);
            if (!cashInput.value) {
                changeEl.textContent = 'Hitung kembalian otomatis';
                changeEl.className = 'mt-2 text-[11px] font-extrabold text-[#7C6D5D]';
            } else if (cash < total) {
                changeEl.textContent = 'Kurang ' + formatRp(total - cash);
                changeEl.className = 'mt-2 text-[11px] font-extrabold text-[#DC2626]';
            } else {
                changeEl.textContent = 'Kembalian ' + formatRp(cash - total);
                changeEl.className = 'mt-2 text-[11px] font-extrabold text-[#166534]';
            }
        }
    };

    document.querySelectorAll('[data-pos-qty]').forEach((btn) => {
        btn.addEventListener('click', () => {
            qtyInput.value = btn.dataset.posQty || '1';
            updateTotals();
        });
    });

    productSelect.addEventListener('change', updateTotals);
    qtyInput.addEventListener('input', updateTotals);
    if (cashInput) cashInput.addEventListener('input', updateTotals);
    updateTotals();
})();
</script>
