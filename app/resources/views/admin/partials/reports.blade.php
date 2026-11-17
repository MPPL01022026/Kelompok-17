@php
    $rangePresets = [
        'today' => 'Hari Ini',
        '7d' => '7 Hari',
        '30d' => '30 Hari',
        'month' => 'Bulan Ini',
        'all' => 'Semua',
    ];
    $exportUrl = route('admin.reports.export', [
        'range' => $report['range'],
        'source' => $report['source'],
        'start_date' => $report['start_date'],
        'end_date' => $report['end_date'],
    ]);
@endphp

<div class="space-y-6">
    {{-- Header Banner Laporan UMKM --}}
    <section
        class="relative overflow-hidden rounded-[28px] border border-[#EBE1D0] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white shadow-sm sm:p-8 print:bg-none print:text-[#1E1915]">
        <div
            class="pointer-events-none absolute -top-16 -right-12 h-48 w-48 rounded-full bg-[#F59E0B]/25 blur-2xl print:hidden">
        </div>
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span
                    class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-black tracking-wide text-[#FEF08A] uppercase print:bg-[#FEF3C7] print:text-[#92400E]">
                    Pembukuan &amp; Analitik UMKM
                </span>
                <h1
                    class="mt-2.5 font-['Outfit'] text-2xl font-black tracking-tight text-white sm:text-3xl print:text-[#1E1915]">
                    Laporan penjualan &amp; pembukuan UMKM
                </h1>
                <p class="mt-1 text-sm font-medium text-white/85 print:text-[#4A3B31]">
                    Periode aktif: <span class="font-black">{{ $report['label'] }}</span> ({{ $report['start_date'] }}
                    s.d. {{ $report['end_date'] }}) · {{ $settings['store_name'] ?? 'Ayamo' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 print:hidden">
                <a href="{{ $exportUrl }}"
                    class="inline-flex items-center gap-2 rounded-2xl bg-[#F59E0B] px-4 py-2.5 text-xs font-black text-[#1E1915] shadow-xs transition hover:bg-[#D97706]">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Unduh CSV (Excel)</span>
                </a>
                <button type="button" onclick="window.print()"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-xs font-black text-[#B83809] shadow-xs transition hover:bg-[#FEF3C7]">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Bar Filter Periode & Sumber --}}
    <section class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs sm:p-5 print:hidden">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-wrap gap-2">
                @foreach ($rangePresets as $presetKey => $presetLabel)
                    @php $activePreset = $report['range'] === $presetKey; @endphp
                    <a href="/admin?tab=reports&range={{ $presetKey }}&source={{ $report['source'] }}"
                        class="rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition {{ $activePreset ? 'border-[#B83809] bg-[#B83809] text-white shadow-2xs' : 'border-[#EBE1D0] bg-[#FAF4EA] text-[#4A3B31] hover:border-[#B83809]/40' }}">
                        {{ $presetLabel }}
                    </a>
                @endforeach
            </div>

            <form method="get" action="/admin" class="flex flex-wrap items-center gap-2.5">
                <input type="hidden" name="tab" value="reports">
                <input type="hidden" name="range" value="custom">

                <div class="flex items-center gap-1.5">
                    <label for="report-start-date" class="text-[11px] font-black text-[#7C6D5D]">Dari</label>
                    <input id="report-start-date" type="date" name="start_date" value="{{ $report['start_date'] }}"
                        class="rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3 py-1.5 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none">
                </div>

                <div class="flex items-center gap-1.5">
                    <label for="report-end-date" class="text-[11px] font-black text-[#7C6D5D]">Sampai</label>
                    <input id="report-end-date" type="date" name="end_date" value="{{ $report['end_date'] }}"
                        class="rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3 py-1.5 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none">
                </div>

                <select name="source" aria-label="Filter sumber transaksi"
                    class="rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3 py-1.5 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none">
                    <option value="all" @selected($report['source'] === 'all')>Semua Sumber</option>
                    <option value="online" @selected($report['source'] === 'online')>Online (Web &amp; Mobile)</option>
                    <option value="manual" @selected($report['source'] === 'manual')>Kasir Manual</option>
                </select>

                <button type="submit"
                    class="cursor-pointer rounded-xl bg-[#1E1915] px-4 py-1.5 text-xs font-black text-white transition hover:bg-[#3A2E25]">
                    Terapkan Filter
                </button>
            </form>
        </div>
    </section>

    {{-- 4 Kartu Eksekutif UMKM --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs">
            <span class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Omzet Periode Terpilih</span>
            <p class="mt-2 font-['Outfit'] text-2xl font-black text-[#B83809] sm:text-3xl">
                Rp{{ number_format($report['total_revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $report['total_transactions'] }} transaksi · {{ $report['total_units'] }} porsi · Rata-rata
                Rp{{ number_format($report['avg_order_value'], 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs">
            <span class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Pendapatan Online vs
                Kasir</span>
            <p class="mt-2 font-['Outfit'] text-xl font-black text-[#166534]">
                Online: Rp{{ number_format($report['online_revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-0.5 font-['Outfit'] text-sm font-black text-[#92400E]">
                Kasir: Rp{{ number_format($report['manual_revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                Porsi {{ $report['online_share_pct'] }}% online · {{ $report['manual_share_pct'] }}% kasir
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs">
            <span class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Potensi Antrean &amp;
                Konversi</span>
            <p class="mt-2 font-['Outfit'] text-2xl font-black text-[#1E1915] sm:text-3xl">
                Rp{{ number_format($report['pipeline_revenue'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ $report['pipeline_orders'] }} pesanan aktif diproses · Konversi selesai
                {{ $report['conversion_rate'] }}%
            </p>
        </article>

        <article class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs">
            <span class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Valuasi Stok Katalog</span>
            <p class="mt-2 font-['Outfit'] text-2xl font-black text-[#1E1915] sm:text-3xl">
                Rp{{ number_format($report['inventory_value'], 0, ',', '.') }}
            </p>
            <p class="mt-1.5 text-xs font-semibold text-[#7C6D5D]">
                {{ number_format($report['inventory_units'], 0, ',', '.') }} porsi siap jual pada
                {{ $stats['active_products'] }} menu aktif
            </p>
        </article>
    </section>

    {{-- Breakdown Kanal & Metode Pembayaran --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Proporsi Kanal Penjualan --}}
        <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
            <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Kontribusi Kanal Penjualan</h2>
            <p class="text-xs font-semibold text-[#7C6D5D]">Perbandingan omzet pesanan pelanggan (Web/Mobile) dan kasir
                walk-in.</p>

            <div class="mt-5 space-y-4">
                <div>
                    <div class="flex items-center justify-between text-xs font-black">
                        <span class="text-[#166534]">Pesanan Online ({{ $report['online_transactions'] }} trx)</span>
                        <span class="text-[#1E1915]">Rp{{ number_format($report['online_revenue'], 0, ',', '.') }}
                            ({{ $report['online_share_pct'] }}%)</span>
                    </div>
                    <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-[#EFE4D2]">
                        <div class="h-full rounded-full bg-[#16A34A]"
                            style="width: {{ min(100, $report['online_share_pct']) }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs font-black">
                        <span class="text-[#92400E]">Kasir Manual ({{ $report['manual_transactions'] }} trx)</span>
                        <span class="text-[#1E1915]">Rp{{ number_format($report['manual_revenue'], 0, ',', '.') }}
                            ({{ $report['manual_share_pct'] }}%)</span>
                    </div>
                    <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-[#EFE4D2]">
                        <div class="h-full rounded-full bg-[#F59E0B]"
                            style="width: {{ min(100, $report['manual_share_pct']) }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Breakdown Metode Pembayaran Pesanan --}}
        <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
            <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Metode Pembayaran Pesanan Selesai</h2>
            <p class="text-xs font-semibold text-[#7C6D5D]">Rekap pesanan online terkonfirmasi berdasarkan cara bayar
                pelanggan.</p>

            <div class="mt-5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                @foreach ($report['payment_breakdown'] as $method)
                    <div class="rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-4">
                        <p class="text-xs font-black text-[#4A3B31]">{{ $method['label'] }}</p>
                        <p class="mt-2 font-['Outfit'] text-xl font-black text-[#B83809]">
                            Rp{{ number_format($method['revenue'], 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-[11px] font-semibold text-[#7C6D5D]">
                            {{ $method['orders'] }} pesanan dikonfirmasi
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    {{-- Tabel Kontribusi per Menu & Rekap Buku Kas Harian --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Kontribusi per Menu UMKM --}}
        <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Kontribusi per Menu UMKM</h2>
                    <p class="text-xs font-semibold text-[#7C6D5D]">Peringkat varian dengan pendapatan terbesar pada
                        periode ini.</p>
                </div>
                <span class="rounded-full bg-[#FAF4EA] px-3 py-1 text-xs font-black text-[#4A3B31]">
                    {{ count($report['product_breakdown']) }} menu terjual
                </span>
            </div>

            @if (empty($report['product_breakdown']))
                <p class="mt-5 rounded-2xl bg-[#FAF4EA] p-6 text-center text-xs font-semibold text-[#7C6D5D]">
                    Belum ada penjualan menu pada filter periode ini.
                </p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr
                                class="border-b border-[#EBE1D0] text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">
                                <th class="py-2.5 pr-3">Menu</th>
                                <th class="px-3 py-2.5 text-right">Porsi</th>
                                <th class="px-3 py-2.5 text-right">Omzet</th>
                                <th class="px-3 py-2.5 text-right">Porsi %</th>
                                <th class="py-2.5 pl-3 text-right">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EBE1D0]/70">
                            @foreach ($report['product_breakdown'] as $prod)
                                <tr>
                                    <td class="py-3 pr-3">
                                        <p class="font-black text-[#1E1915]">{{ $prod['name'] }}</p>
                                        <p class="text-[10px] font-semibold text-[#7C6D5D]">{{ $prod['category'] }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-right font-black text-[#1E1915]">{{ $prod['units'] }}</td>
                                    <td class="px-3 py-3 text-right font-['Outfit'] font-black text-[#B83809]">
                                        Rp{{ number_format($prod['revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-[#4A3B31]">{{ $prod['share_pct'] }}%</td>
                                    <td
                                        class="py-3 pl-3 text-right font-black {{ $prod['stock'] <= 5 ? 'text-[#991B1B]' : 'text-[#166534]' }}">
                                        {{ $prod['stock'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Buku Kas Harian --}}
        <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Rekap Buku Kas Harian</h2>
                    <p class="text-xs font-semibold text-[#7C6D5D]">Ringkasan pemasukan harian terakumulasi per tanggal.
                    </p>
                </div>
                <span class="rounded-full bg-[#FAF4EA] px-3 py-1 text-xs font-black text-[#4A3B31]">
                    {{ count($report['daily_rows']) }} hari tercatat
                </span>
            </div>

            @if (empty($report['daily_rows']))
                <p class="mt-5 rounded-2xl bg-[#FAF4EA] p-6 text-center text-xs font-semibold text-[#7C6D5D]">
                    Belum ada catatan buku kas pada filter periode ini.
                </p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr
                                class="border-b border-[#EBE1D0] text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">
                                <th class="py-2.5 pr-3">Tanggal</th>
                                <th class="px-3 py-2.5 text-right">Online</th>
                                <th class="px-3 py-2.5 text-right">Kasir</th>
                                <th class="px-3 py-2.5 text-right">Porsi</th>
                                <th class="py-2.5 pl-3 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EBE1D0]/70">
                            @foreach ($report['daily_rows'] as $daily)
                                <tr>
                                    <td class="py-3 pr-3 font-black text-[#1E1915]">{{ $daily['label'] }}</td>
                                    <td class="px-3 py-3 text-right font-bold text-[#166534]">{{ $daily['online_count'] }}</td>
                                    <td class="px-3 py-3 text-right font-bold text-[#92400E]">{{ $daily['manual_count'] }}</td>
                                    <td class="px-3 py-3 text-right font-black text-[#4A3B31]">{{ $daily['units'] }}</td>
                                    <td class="py-3 pl-3 text-right font-['Outfit'] font-black text-[#B83809]">
                                        Rp{{ number_format($daily['revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    {{-- Rincian Buku Kas Transaksi --}}
    <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Rincian Transaksi (Buku Kas Detail)</h2>
                <p class="text-xs font-semibold text-[#7C6D5D]">Seluruh pemasukan terkonfirmasi baik dari checkout
                    pelanggan maupun kasir.</p>
            </div>

            <div class="relative w-full sm:w-64 print:hidden">
                <input id="report-tx-search" type="search" placeholder="Cari pelanggan atau kode..."
                    class="w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2 text-xs font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none">
            </div>
        </div>

        @if (empty($report['transactions']))
            <p class="mt-5 rounded-2xl bg-[#FAF4EA] p-6 text-center text-xs font-semibold text-[#7C6D5D]">
                Tidak ada transaksi tercatat pada filter periode dan sumber yang dipilih.
            </p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#EBE1D0] text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">
                            <th class="py-2.5 pr-3">Kode / ID</th>
                            <th class="px-3 py-2.5">Waktu</th>
                            <th class="px-3 py-2.5">Kanal</th>
                            <th class="px-3 py-2.5">Pelanggan</th>
                            <th class="px-3 py-2.5">Rincian Menu</th>
                            <th class="px-3 py-2.5 text-right">Porsi</th>
                            <th class="py-2.5 pl-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EBE1D0]/70" id="report-tx-body">
                        @foreach ($report['transactions'] as $tx)
                            @php
                                $searchBlob = mb_strtolower($tx['display_id'] . ' ' . $tx['customer_name'] . ' ' . $tx['items_summary']);
                            @endphp
                            <tr data-report-tx data-search="{{ $searchBlob }}">
                                <td class="py-3 pr-3 font-mono text-[11px] font-bold text-[#4A3B31]">{{ $tx['display_id'] }}
                                </td>
                                <td class="px-3 py-3 font-semibold text-[#7C6D5D]">{{ $tx['time_label'] }}</td>
                                <td class="px-3 py-3">
                                    <span
                                        class="rounded-full px-2.5 py-0.5 text-[10px] font-black {{ $tx['source'] === 'manual' ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-[#DCFCE7] text-[#166534]' }}">
                                        {{ $tx['source'] === 'manual' ? 'Kasir' : 'Online' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 font-black text-[#1E1915]">{{ $tx['customer_name'] }}</td>
                                <td class="px-3 py-3 font-medium text-[#4A3B31]">{{ $tx['items_summary'] }}</td>
                                <td class="px-3 py-3 text-right font-black text-[#1E1915]">{{ $tx['units'] }}</td>
                                <td class="py-3 pl-3 text-right font-['Outfit'] font-black text-[#B83809]">
                                    Rp{{ number_format($tx['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<script>
    (function () {
        const searchInput = document.getElementById('report-tx-search');
        const rows = Array.from(document.querySelectorAll('[data-report-tx]'));
        if (!searchInput || !rows.length) return;

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            rows.forEach((row) => {
                const matches = !query || (row.dataset.search || '').includes(query);
                row.classList.toggle('hidden', !matches);
            });
        });
    })();
</script>
