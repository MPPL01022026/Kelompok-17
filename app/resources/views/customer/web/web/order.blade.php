@extends('layouts.web')
@section('title', 'Status Pesanan ' . $order->code . ' — Ayamo')

@section('content')
    @php
        $statusMeta = match ($order->status) {
            'processing' => [
                'label' => 'Sedang Diproses',
                'hint' => 'Dapur sedang menggoreng hangat ayam crispy pesananmu.',
                'badge' => 'bg-[#FFEDD5] text-[#9A3412]',
                'step' => 2,
            ],
            'ready' => [
                'label' => 'Siap Diambil',
                'hint' => 'Pesanan sudah siap! Silakan datang ke outlet dan tunjukkan kode pesanan.',
                'badge' => 'bg-[#E0F2FE] text-[#075985]',
                'step' => 3,
            ],
            'confirmed', 'completed' => [
                'label' => 'Pesanan Dikonfirmasi',
                'hint' => 'Pesanan telah selesai diterima dan dicatat pada riwayat toko.',
                'badge' => 'bg-[#DCFCE7] text-[#166534]',
                'step' => 4,
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'hint' => 'Pesanan telah dibatalkan dan stok menu dikembalikan ke outlet.',
                'badge' => 'bg-[#FEE2E2] text-[#991B1B]',
                'step' => 0,
            ],
            default => [
                'label' => 'Menunggu Konfirmasi',
                'hint' => 'Pesanan sudah masuk ke sistem kasir dan menunggu konfirmasi admin.',
                'badge' => 'bg-[#FEF3C7] text-[#92400E]',
                'step' => 1,
            ],
        };
        $timelineSteps = [
            ['num' => 1, 'label' => 'Menunggu Konfirmasi'],
            ['num' => 2, 'label' => 'Sedang Diproses'],
            ['num' => 3, 'label' => 'Siap Diambil'],
            ['num' => 4, 'label' => 'Selesai'],
        ];
        $orderSettings = \App\Models\Settings::getMap();
        $orderWaDigits = preg_replace('/[^0-9]/', '', $orderSettings['phone_number'] ?? '62895346200506');
        if (str_starts_with($orderWaDigits, '0')) {
            $orderWaDigits = '62' . substr($orderWaDigits, 1);
        }
        $orderWaText = rawurlencode("Halo Ayamo, saya ingin menanyakan status pesanan {$order->code}.");
    @endphp

    <main id="main-content" tabindex="-1" class="mx-auto min-h-[65vh] max-w-4xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('web.history') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-[#B83809] hover:underline">
                <span>← Kembali ke Riwayat Pesanan</span>
            </a>
            <a href="{{ route('web.menu') }}" class="text-xs font-extrabold text-[#7C6D5D] transition hover:text-[#B83809]">
                Lihat Katalog Menu →
            </a>
        </div>

        <section class="mt-5 overflow-hidden rounded-[2.5rem] border border-[#EBE1D0] bg-[#FFFDF9] shadow-sm">
            {{-- Header Band --}}
            <div class="relative isolate overflow-hidden bg-gradient-to-br from-[#EA580C] via-[#B83809] to-[#7C2405] p-6 text-white sm:p-8">
                <div class="pointer-events-none absolute -top-16 -right-12 -z-10 h-48 w-48 rounded-full bg-[#F3B825]/25 blur-2xl"></div>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <span class="text-xs font-bold text-white/80">Kode Pesanan</span>
                        <div class="mt-1 flex flex-wrap items-center gap-3">
                            <h1 class="font-['Outfit'] text-3xl font-black tracking-wide text-[#FEF08A] sm:text-4xl">
                                {{ $order->code }}
                            </h1>
                            <button type="button" onclick="copyOrderCode('{{ $order->code }}', this)"
                                class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-[11px] font-extrabold text-white backdrop-blur-xs transition hover:bg-white/20 active:scale-95">
                                <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>
                        @if ($order->created_at)
                            <p class="mt-2 text-xs font-medium text-white/75">
                                Dibuat pada {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </p>
                        @endif
                    </div>
                    <span class="w-fit rounded-full px-4 py-1.5 text-xs font-black shadow-2xs {{ $statusMeta['badge'] }}">
                        {{ $statusMeta['label'] }}
                    </span>
                </div>
                <p class="mt-4 rounded-2xl border border-white/15 bg-black/15 px-4 py-2.5 text-xs font-semibold text-white/95 backdrop-blur-xs">
                    {{ $statusMeta['hint'] }}
                </p>
            </div>

            {{-- Progress Strip --}}
            @if ($order->status === 'cancelled')
                <div class="border-b border-[#FECACA] bg-[#FEF2F2] px-6 py-4 text-sm font-bold text-[#991B1B] sm:px-8">
                    Pesanan ini telah dibatalkan. Silakan hubungi WhatsApp toko atau buat pesanan baru jika masih membutuhkan.
                </div>
            @else
                <div class="border-b border-[#EBE1D0] bg-[#FAF4EA]/60 px-6 py-4 sm:px-8">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($timelineSteps as $timelineStep)
                            @php
                                $reached = $statusMeta['step'] >= $timelineStep['num'];
                                $done = $statusMeta['step'] > $timelineStep['num'] || $statusMeta['step'] === 4;
                                $current = $statusMeta['step'] === $timelineStep['num'] && $statusMeta['step'] < 4;
                            @endphp
                            <div class="flex items-center gap-2.5 rounded-2xl border px-3 py-2.5 transition {{ $current ? 'border-[#B83809]/45 bg-white shadow-xs ring-1 ring-[#F3B825]/50' : ($reached ? 'border-[#EA580C]/25 bg-white shadow-2xs' : 'border-[#EBE1D0] bg-transparent opacity-60') }}">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-black {{ $done ? 'bg-[#16A34A] text-white' : ($current ? 'bg-[#F3B825] text-[#1E1915]' : 'bg-[#EBE1D0] text-[#7C6D5D]') }}">
                                    @if ($done)
                                        <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    @else
                                        {{ $timelineStep['num'] }}
                                    @endif
                                </span>
                                <span class="text-xs font-extrabold {{ $reached ? 'text-[#1E1915]' : 'text-[#7C6D5D]' }}">
                                    {{ $timelineStep['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Body Grid --}}
            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1fr_290px]">
                {{-- Left: Item Breakdown --}}
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Rincian Menu</h2>
                        <span class="text-xs font-bold text-[#8C7E70]">{{ collect($order->parsed_items)->sum(fn ($i) => (int) ($i['quantity'] ?? 1)) }} Porsi</span>
                    </div>
                    <div class="mt-4 divide-y divide-[#EFE6D8]">
                        @foreach ($order->parsed_items as $item)
                            <div class="flex items-center justify-between gap-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 min-w-8 items-center justify-center rounded-xl bg-[#FAF4EA] px-2 font-['Outfit'] text-xs font-black text-[#B83809]">
                                        {{ $item['quantity'] ?? 1 }}×
                                    </span>
                                    <div>
                                        <p class="text-xs font-black text-[#1E1915]">{{ $item['name'] ?? 'Menu Ayamo' }}</p>
                                        <p class="text-[11px] font-semibold text-[#8C7E70]">
                                            Rp{{ number_format($item['price'] ?? 0, 0, ',', '.') }} / porsi
                                        </p>
                                    </div>
                                </div>
                                <strong class="font-['Outfit'] text-sm font-black text-[#1E1915]">
                                    Rp{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}
                                </strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 flex items-center justify-between rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] px-4 py-4">
                        <span class="font-['Outfit'] text-sm font-bold text-[#1E1915]">Total Pembayaran</span>
                        <strong class="font-['Outfit'] text-2xl font-black text-[#B83809]">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                {{-- Right: Customer Data & Action --}}
                <aside class="h-fit rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] p-5">
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

                    <div class="mt-6 space-y-2.5">
                        @if (in_array($order->payment_method, ['whatsapp', 'transfer'], true) && $order->status !== 'cancelled')
                            <a href="{{ route('web.payment', $order->id) }}"
                                class="flex h-11 w-full items-center justify-center rounded-2xl bg-[#B83809] text-xs font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-95">
                                Lihat Transfer Bank &amp; QRIS →
                            </a>
                        @endif

                        <form method="post" action="{{ route('web.orders.reorder', $order->id) }}">
                            @csrf
                            <button type="submit"
                                class="flex h-11 w-full cursor-pointer items-center justify-center rounded-2xl bg-[#F3B825] text-xs font-black text-[#1E1915] shadow-2xs transition hover:bg-[#FACC15] active:scale-95">
                                Pesan Menu Ini Lagi
                            </button>
                        </form>

                        <a href="https://wa.me/{{ $orderWaDigits }}?text={{ $orderWaText }}"
                            target="_blank" rel="noopener"
                            class="flex h-10 w-full items-center justify-center gap-1.5 rounded-2xl border border-[#DCFCE7] bg-[#F0FDF4] text-xs font-extrabold text-[#166534] transition hover:border-[#86EFAC] hover:bg-[#DCFCE7]">
                            <span>Hubungi Outlet (WhatsApp)</span>
                            <span>↗</span>
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <script>
        async function copyOrderCode(code, btn) {
            let copied = false;
            try {
                if (navigator.clipboard) {
                    await navigator.clipboard.writeText(code);
                    copied = true;
                }
            } catch (e) {
                copied = false;
            }
            window.AyamoNotify?.(copied ? 'Kode pesanan disalin!' : 'Salin kode secara manual.', copied ? 'success' : 'warning');
            const label = btn?.querySelector('span');
            if (label && copied) {
                const orig = label.textContent;
                label.textContent = 'Disalin';
                setTimeout(() => { label.textContent = orig; }, 1800);
            }
        }
    </script>
@endsection
