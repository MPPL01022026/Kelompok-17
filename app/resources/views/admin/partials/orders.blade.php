@php
    $statusFilters = [
        ['key' => 'all', 'label' => 'Semua', 'count' => $orders->count()],
        ['key' => 'pending', 'label' => 'Menunggu', 'count' => $stats['pending_orders']],
        ['key' => 'processing', 'label' => 'Diproses', 'count' => $orders->where('status', 'processing')->count()],
        ['key' => 'ready', 'label' => 'Siap Diambil', 'count' => $orders->where('status', 'ready')->count()],
        ['key' => 'completed', 'label' => 'Selesai', 'count' => $stats['completed_orders']],
        ['key' => 'cancelled', 'label' => 'Dibatalkan', 'count' => $stats['cancelled_orders']],
    ];
@endphp

<div class="space-y-6">
    {{-- Header Banner --}}
    <section class="relative overflow-hidden rounded-[28px] border border-[#EBE1D0] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-black tracking-wide text-[#FEF08A] uppercase">
                    Antrean &amp; Proses Dapur
                </span>
                <h1 class="mt-2.5 font-['Outfit'] text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Pesanan masuk
                </h1>
                <p class="mt-1 text-sm font-medium text-white/85">
                    Alur: 1. Menunggu → 2. Diproses → 3. Siap Diambil → 4. Selesai (masuk omzet). Batalkan untuk kembalikan stok.
                </p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-right backdrop-blur-xs">
                <p class="text-[11px] font-bold text-[#FEF08A]">Pesanan Aktif</p>
                <p class="font-['Outfit'] text-2xl font-black text-white">
                    {{ $stats['pending_orders'] + $stats['processing_orders'] }} antrean
                </p>
            </div>
        </div>
    </section>

    {{-- Summary Mini Cards --}}
    <section class="grid grid-cols-2 gap-3.5 sm:grid-cols-4">
        <div class="rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs">
            <p class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Menunggu</p>
            <p class="mt-1 font-['Outfit'] text-2xl font-black text-[#92400E]">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs">
            <p class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Diproses / Siap</p>
            <p class="mt-1 font-['Outfit'] text-2xl font-black text-[#B83809]">{{ $stats['processing_orders'] }}</p>
        </div>
        <div class="rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs">
            <p class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Selesai</p>
            <p class="mt-1 font-['Outfit'] text-2xl font-black text-[#166534]">{{ $stats['completed_orders'] }}</p>
        </div>
        <div class="rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs">
            <p class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Dibatalkan</p>
            <p class="mt-1 font-['Outfit'] text-2xl font-black text-[#991B1B]">{{ $stats['cancelled_orders'] }}</p>
        </div>
    </section>

    {{-- Alur Pesanan Strip --}}
    <section class="rounded-[22px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="text-xs font-black tracking-wide text-[#4A3B31] uppercase">Alur Pesanan</span>
            <span class="text-[11px] font-semibold text-[#7C6D5D]">Stok dikunci saat order masuk, omzet dicatat di tahap 4</span>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 text-xs sm:grid-cols-4">
            <div class="rounded-xl border border-[#FDE68A] bg-[#FEFCE8] px-3 py-2">
                <p class="font-black text-[#92400E]">1. Menunggu</p>
                <p class="text-[11px] font-semibold text-[#854D0E]">Order baru · stok dikunci</p>
            </div>
            <div class="rounded-xl border border-[#FDBA74] bg-[#FFF7ED] px-3 py-2">
                <p class="font-black text-[#9A3412]">2. Diproses</p>
                <p class="text-[11px] font-semibold text-[#9A3412]/80">Sedang digoreng dapur</p>
            </div>
            <div class="rounded-xl border border-[#BAE6FD] bg-[#F0F9FF] px-3 py-2">
                <p class="font-black text-[#075985]">3. Siap Diambil</p>
                <p class="text-[11px] font-semibold text-[#075985]/80">Tunggu ambil / bayar</p>
            </div>
            <div class="rounded-xl border border-[#BBF7D0] bg-[#F0FDF4] px-3 py-2">
                <p class="font-black text-[#166534]">4. Selesai</p>
                <p class="text-[11px] font-semibold text-[#166534]/80">Masuk omzet &amp; laporan</p>
            </div>
        </div>
    </section>

    {{-- Search & Filter Toolbar --}}
    <section class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs sm:p-5">
        <div class="flex flex-col gap-3.5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2" id="order-filter-pills">
                @foreach ($statusFilters as $idx => $filter)
                    <button
                        type="button"
                        data-order-status="{{ $filter['key'] }}"
                        class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition {{ $idx === 0 ? 'border-[#B83809] bg-[#B83809] text-white' : 'border-[#EBE1D0] bg-[#FAF4EA] text-[#4A3B31] hover:border-[#B83809]/40' }}"
                    >
                        <span>{{ $filter['label'] }}</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-black {{ $idx === 0 ? 'bg-white/20 text-white' : 'bg-white text-[#7C6D5D]' }}">
                            {{ $filter['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>

            <div class="relative w-full lg:w-72">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#9A8B7A]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input
                    id="order-search-input"
                    type="search"
                    placeholder="Cari kode AYM, nama, no. HP..."
                    class="w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 py-2.5 pr-4 pl-10 text-xs font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
                >
            </div>
        </div>
    </section>

    {{-- Orders List --}}
    @if ($orders->isEmpty())
        <section class="rounded-[28px] border border-dashed border-[#DFD2BD] bg-[#FFFDF9] p-12 text-center">
            <p class="font-['Outfit'] text-lg font-black text-[#1E1915]">Belum ada pesanan masuk</p>
            <p class="mt-1 text-sm font-medium text-[#7C6D5D]">
                Pesanan baru dari pelanggan web maupun mobile akan langsung muncul di halaman ini.
            </p>
        </section>
    @else
        <div class="space-y-4" id="orders-list">
            @foreach ($orders as $order)
                @php
                    $normalizedGroup = in_array($order->status, ['confirmed', 'completed'], true) ? 'completed' : $order->status;
                    $statusInfo = match ($order->status) {
                        'processing' => ['label' => 'Sedang Diproses', 'step' => 'Tahap 2/4', 'class' => 'bg-[#FFEDD5] text-[#9A3412]'],
                        'ready' => ['label' => 'Siap Diambil', 'step' => 'Tahap 3/4', 'class' => 'bg-[#E0F2FE] text-[#075985]'],
                        'confirmed', 'completed' => ['label' => 'Dikonfirmasi', 'step' => 'Tahap 4/4', 'class' => 'bg-[#DCFCE7] text-[#166534]'],
                        'cancelled' => ['label' => 'Dibatalkan', 'step' => 'Batal', 'class' => 'bg-[#FEE2E2] text-[#991B1B]'],
                        default => ['label' => 'Menunggu Konfirmasi', 'step' => 'Tahap 1/4', 'class' => 'bg-[#FEF3C7] text-[#92400E]'],
                    };
                    $digitsPhone = preg_replace('/\D+/', '', (string) $order->phone);
                    if ($digitsPhone && str_starts_with($digitsPhone, '0')) {
                        $digitsPhone = '62' . substr($digitsPhone, 1);
                    }
                    $searchBlob = mb_strtolower($order->id . ' ' . $order->customer_name . ' ' . $order->phone . ' ' . $order->items_summary);
                @endphp

                <article
                    data-order-card
                    data-status-group="{{ $normalizedGroup }}"
                    data-search="{{ $searchBlob }}"
                    class="rounded-[26px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs transition hover:border-[#EA580C]/40 sm:p-6"
                >
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        {{-- Order Details --}}
                        <div class="min-w-0 flex-1 space-y-3.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-['Outfit'] text-xl font-black text-[#1E1915]">{{ $order->code }}</span>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusInfo['class'] }}">
                                    {{ $statusInfo['label'] }}
                                </span>
                                <span class="rounded-full border border-[#DFD2BD] bg-white px-2.5 py-1 text-[11px] font-black text-[#4A3B31]">
                                    {{ $statusInfo['step'] }}
                                </span>
                                <span class="rounded-full bg-[#FAF4EA] px-3 py-1 text-[11px] font-black text-[#4A3B31] uppercase">
                                    {{ $order->payment_method ?: 'Transfer' }}
                                </span>
                                @if ($order->stock_reserved)
                                    <span class="rounded-full bg-[#DCFCE7]/80 px-3 py-1 text-[11px] font-black text-[#166534]">
                                        Stok direservasi
                                    </span>
                                @elseif ($order->status === 'cancelled')
                                    <span class="rounded-full bg-[#FEE2E2]/80 px-3 py-1 text-[11px] font-black text-[#991B1B]">
                                        Stok dikembalikan
                                    </span>
                                @else
                                    <span class="rounded-full bg-[#F3E7D3] px-3 py-1 text-[11px] font-black text-[#7C6D5D]">
                                        Reservasi lama
                                    </span>
                                @endif
                            </div>

                            {{-- Customer Row --}}
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                                <div>
                                    <span class="text-xs font-semibold text-[#7C6D5D]">Pelanggan:</span>
                                    <span class="ml-1 font-black text-[#1E1915]">{{ $order->customer_name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-[#7C6D5D]">HP:</span>
                                    <span class="font-bold text-[#4A3B31]">{{ $order->phone }}</span>
                                    @if ($digitsPhone)
                                        <a
                                            href="https://wa.me/{{ $digitsPhone }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center gap-1 rounded-full bg-[#DCFCE7] px-2.5 py-0.5 text-[11px] font-black text-[#166534] transition hover:bg-[#BBF7D0]"
                                        >
                                            Chat WA ↗
                                        </a>
                                    @endif
                                </div>
                                <div class="text-xs font-semibold text-[#7C6D5D]">
                                    Masuk: {{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y, H:i') : '-' }}
                                </div>
                            </div>

                            {{-- Items List --}}
                            <div class="flex flex-wrap gap-2">
                                @foreach ($order->parsed_items as $item)
                                    <div class="inline-flex items-center gap-2 rounded-xl border border-[#EBE1D0] bg-[#FAF4EA] px-3 py-1.5 text-xs">
                                        <span class="rounded-md bg-[#F59E0B] px-1.5 py-0.5 text-[10px] font-black text-[#1E1915]">
                                            {{ $item['quantity'] ?? 1 }}×
                                        </span>
                                        <span class="font-extrabold text-[#1E1915]">{{ $item['name'] ?? 'Produk' }}</span>
                                        <span class="font-semibold text-[#7C6D5D]">
                                            Rp{{ number_format(((int) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 1)), 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if (! empty($order->note))
                                <div class="rounded-2xl border border-[#FDE68A] bg-[#FEFCE8] px-3.5 py-2 text-xs font-semibold text-[#854D0E]">
                                    <span class="font-black">Catatan pembeli:</span> {{ $order->note }}
                                </div>
                            @endif
                        </div>

                        {{-- Price & Status Actions --}}
                        <div class="flex shrink-0 flex-col justify-between gap-4 border-t border-[#EBE1D0] pt-4 lg:w-72 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-6">
                            <div>
                                <p class="text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">Total Tagihan</p>
                                <p class="mt-0.5 font-['Outfit'] text-2xl font-black text-[#B83809]">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if ($order->status === 'pending')
                                    <form action="/admin/orders/{{ $order->id }}/status" method="post">
                                        @csrf
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="cursor-pointer rounded-xl border border-[#EA580C]/40 bg-[#FFF7ED] px-3 py-2 text-xs font-black text-[#9A3412] transition hover:bg-[#FFEDD5]">
                                            1→2 Proses Dapur
                                        </button>
                                    </form>
                                    <form action="/admin/orders/{{ $order->id }}/confirm" method="post">
                                        @csrf
                                        <button type="submit" class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#B83809] px-3.5 py-2 text-xs font-black text-white shadow-2xs transition hover:bg-[#992D06]">
                                            <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            <span>Langsung Selesai</span>
                                        </button>
                                    </form>
                                @elseif ($order->status === 'processing')
                                    <form action="/admin/orders/{{ $order->id }}/status" method="post">
                                        @csrf
                                        <input type="hidden" name="status" value="ready">
                                        <button type="submit" class="cursor-pointer rounded-xl border border-[#0284C7]/40 bg-[#F0F9FF] px-3 py-2 text-xs font-black text-[#075985] transition hover:bg-[#E0F2FE]">
                                            2→3 Siap Diambil
                                        </button>
                                    </form>
                                    <form action="/admin/orders/{{ $order->id }}/confirm" method="post">
                                        @csrf
                                        <button type="submit" class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#B83809] px-3.5 py-2 text-xs font-black text-white shadow-2xs transition hover:bg-[#992D06]">
                                            <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            <span>Langsung Selesai</span>
                                        </button>
                                    </form>
                                @elseif ($order->status === 'ready')
                                    <form action="/admin/orders/{{ $order->id }}/confirm" method="post">
                                        @csrf
                                        <button type="submit" class="cursor-pointer rounded-xl bg-[#166534] px-3.5 py-2 text-xs font-black text-white shadow-2xs transition hover:bg-[#15803D]">
                                            3→4 Selesai &amp; Catat Omzet
                                        </button>
                                    </form>
                                @elseif ($order->status === 'cancelled')
                                    <form action="/admin/orders/{{ $order->id }}/status" method="post">
                                        @csrf
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-[#DFD2BD] bg-[#FAF4EA] px-3 py-2 text-xs font-black text-[#4A3B31] transition hover:bg-white">
                                            <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                                            <span>Aktifkan ke Diproses</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-[#DCFCE7] px-3 py-2 text-xs font-black text-[#166534]">
                                        <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        <span>Tercatat di omzet toko</span>
                                    </span>
                                @endif

                                @if (in_array($order->status, ['pending', 'processing', 'ready'], true))
                                    <form
                                        action="/admin/orders/{{ $order->id }}/status"
                                        method="post"
                                        data-confirm="Stok produk pada pesanan {{ $order->code }} akan dikembalikan otomatis ke katalog."
                                        data-confirm-title="Batalkan Pesanan {{ $order->code }}?"
                                        data-confirm-ok="Ya, Batalkan Pesanan"
                                        data-confirm-cancel="Kembali"
                                        data-confirm-variant="danger"
                                    >
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-3 py-2 text-xs font-black text-[#991B1B] transition hover:bg-[#FEE2E2]">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p id="orders-empty-filter" class="hidden rounded-[24px] border border-dashed border-[#DFD2BD] bg-[#FFFDF9] p-8 text-center text-sm font-bold text-[#7C6D5D]">
            Tidak ada pesanan yang cocok dengan filter pencarian tersebut.
        </p>
    @endif
</div>

<script>
(function () {
    const cards = Array.from(document.querySelectorAll('[data-order-card]'));
    const pills = Array.from(document.querySelectorAll('[data-order-status]'));
    const searchInput = document.getElementById('order-search-input');
    const emptyEl = document.getElementById('orders-empty-filter');
    if (!cards.length) return;

    let activeStatus = 'all';

    const applyFilter = () => {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const matchesStatus = activeStatus === 'all' || card.dataset.statusGroup === activeStatus;
            const matchesQuery = !query || (card.dataset.search || '').includes(query);
            const show = matchesStatus && matchesQuery;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    };

    pills.forEach((pill) => {
        pill.addEventListener('click', () => {
            activeStatus = pill.dataset.orderStatus || 'all';
            pills.forEach((p) => {
                const isSelected = p === pill;
                p.className = isSelected
                    ? 'inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition border-[#B83809] bg-[#B83809] text-white'
                    : 'inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition border-[#EBE1D0] bg-[#FAF4EA] text-[#4A3B31] hover:border-[#B83809]/40';
                const badge = p.querySelector('span:last-child');
                if (badge) {
                    badge.className = isSelected
                        ? 'rounded-full px-1.5 py-0.5 text-[10px] font-black bg-white/20 text-white'
                        : 'rounded-full px-1.5 py-0.5 text-[10px] font-black bg-white text-[#7C6D5D]';
                }
            });
            applyFilter();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilter);
})();
</script>
