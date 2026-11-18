@extends('layouts.web')
@section('title', 'Instruksi Pembayaran ' . $order->code . ' — Ayamo')

@section('content')
    @php
        $isTransfer = in_array($order->payment_method, ['whatsapp', 'transfer'], true);
        $paymentChannels = \App\Models\Settings::getPaymentChannels($settings);
        $enabledTransfers = $paymentChannels['enabled_transfers'];
        $qrisChannel = $paymentChannels['qris'];
        $waChannel = $paymentChannels['whatsapp'];

        $requestedChannel = request()->query('channel', 'bank');
        $activeChannel = in_array($requestedChannel, ['bank', 'qris'], true) ? $requestedChannel : 'bank';
        if ($activeChannel === 'bank' && empty($enabledTransfers) && $qrisChannel['enable']) {
            $activeChannel = 'qris';
        } elseif ($activeChannel === 'qris' && !$qrisChannel['enable'] && !empty($enabledTransfers)) {
            $activeChannel = 'bank';
        }

        $waPhone = preg_replace('/[^0-9]/', '', $settings['phone_number'] ?? '62895346200506');
        if (str_starts_with($waPhone, '0')) {
            $waPhone = '62' . substr($waPhone, 1);
        }
        $waMessageBank = "Halo Ayamo, saya ingin konfirmasi pembayaran pesanan {$order->code} via Transfer Bank. Total: Rp" . number_format($order->total, 0, ',', '.') . ". Berikut bukti pembayarannya.";
        $waMessageQris = "Halo Ayamo, saya ingin konfirmasi pembayaran pesanan {$order->code} via QRIS. Total: Rp" . number_format($order->total, 0, ',', '.') . ". Berikut bukti pembayarannya.";
        $qrisImgSrc = $qrisChannel['image'] !== ''
            ? (str_starts_with($qrisChannel['image'], 'http') || str_starts_with($qrisChannel['image'], '/') ? $qrisChannel['image'] : '/' . ltrim($qrisChannel['image'], '/'))
            : asset('img/qris-default.png');
    @endphp

    <main id="main-content" tabindex="-1"
        class="mx-auto min-h-[65vh] max-w-5xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        {{-- Success Banner --}}
        <div class="text-center">
            <span
                class="inline-flex items-center gap-1.5 rounded-full border border-[#DCFCE7] bg-[#F0FDF4] px-4 py-1.5 text-xs font-black text-[#166534] shadow-2xs">
                <span><svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></span>
                <span>Pesanan Berhasil Dibuat</span>
            </span>
            <h1 class="mt-4 font-['Outfit'] text-3xl font-black text-[#1E1915] sm:text-4xl">
                Terima Kasih, {{ $order->customer_name }}!
            </h1>
            <p class="mt-1 text-xs font-medium text-[#7C6D5D]">
                Simpan kode pesananmu untuk konfirmasi dan pengambilan di outlet.
            </p>
        </div>

        {{-- Detail Cards Grid --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            {{-- Left Card: Kode Pesanan & Total --}}
            <section class="rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
                <p class="text-xs font-bold text-[#8C7E70]">Kode Pesanan</p>
                <p class="mt-1 font-['Outfit'] text-4xl font-black tracking-wide text-[#B83809]">
                    {{ $order->code }}
                </p>

                <div class="my-6 border-t border-dashed border-[#D8CBB8]"></div>

                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#7C6D5D]">Total Pembayaran</span>
                    <strong class="font-['Outfit'] text-2xl font-black text-[#1E1915]">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </strong>
                </div>

                <div class="mt-6 rounded-2xl bg-[#FAF5EB] p-4 border border-[#EBE1D0]">
                    <p class="text-xs leading-relaxed text-[#7C6D5D]">
                        Status pesanan: <b class="text-[#1E1915]">Menunggu Konfirmasi</b>. Admin outlet akan segera
                        menyiapkan pesananmu setelah pembayaran terverifikasi.
                    </p>
                </div>
            </section>

            {{-- Right Card: Instruksi Pembayaran --}}
            <section class="rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
                @if ($isTransfer)
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">PEMBAYARAN
                                NON-TUNAI</span>
                            <h2 class="mt-0.5 font-['Outfit'] text-2xl font-black text-[#1E1915]">Pilih Transfer atau QRIS</h2>
                        </div>
                    </div>

                    {{-- Switcher: Transfer | QRIS --}}
                    <div class="mt-5 grid grid-cols-2 gap-2 rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA] p-1.5"
                        role="tablist" aria-label="Channel Pembayaran">
                        <button type="button" id="web-tab-bank" role="tab"
                            aria-selected="{{ $activeChannel === 'bank' ? 'true' : 'false' }}"
                            onclick="switchWebPayChannel('bank')"
                            class="cursor-pointer rounded-xl px-4 py-2.5 font-['Outfit'] text-xs font-black transition {{ $activeChannel === 'bank' ? 'bg-white text-[#B83809] shadow-2xs' : 'text-[#6B5E50] hover:text-[#1E1915]' }}">
                            Transfer ({{ count($enabledTransfers) }})
                        </button>
                        <button type="button" id="web-tab-qris" role="tab"
                            aria-selected="{{ $activeChannel === 'qris' ? 'true' : 'false' }}"
                            onclick="switchWebPayChannel('qris')"
                            class="cursor-pointer rounded-xl px-4 py-2.5 font-['Outfit'] text-xs font-black transition {{ $activeChannel === 'qris' ? 'bg-white text-[#B83809] shadow-2xs' : 'text-[#6B5E50] hover:text-[#1E1915]' }}">
                            {{ $qrisChannel['name'] }}
                        </button>
                    </div>

                    {{-- Panel Transfer: All Enabled Payment Channels --}}
                    <div id="web-panel-bank" class="mt-5 space-y-3 {{ $activeChannel === 'bank' ? '' : 'hidden' }}">
                        @forelse ($enabledTransfers as $ch)
                            @php $cleanNum = preg_replace('/\s+/', '', $ch['number']); @endphp
                            <div
                                class="flex flex-col justify-between gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] p-4 sm:flex-row sm:items-center">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="rounded-md bg-[#FEF3C7] px-2 py-0.5 text-[10px] font-black tracking-wide text-[#92400E] uppercase">{{ $ch['key'] }}</span>
                                        <p class="font-['Outfit'] text-sm font-black text-[#1E1915]">{{ $ch['name'] }}</p>
                                    </div>
                                    <p class="mt-1 font-['Outfit'] text-xl font-black tracking-wide text-[#B83809]">
                                        {{ $ch['number'] }}
                                    </p>
                                    <p class="text-xs font-semibold text-[#4A3B31]">a.n {{ $ch['holder'] }}</p>
                                </div>
                                <button type="button" onclick="copyWebAccount('{{ $cleanNum }}', this)"
                                    class="shrink-0 cursor-pointer rounded-xl border border-[#D8CBB8] bg-white px-3.5 py-2 text-xs font-extrabold text-[#1E1915] shadow-2xs transition hover:border-[#B83809] hover:text-[#B83809] active:scale-95">
                                    Salin Nomor
                                </button>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-[#DFD2BD] bg-[#FAF5EB] p-5 text-center text-xs font-bold text-[#7C6D5D]">
                                Belum ada rekening transfer aktif. Silakan gunakan tab QRIS atau hubungi kasir.
                            </div>
                        @endforelse
                    </div>

                    {{-- Panel QRIS --}}
                    <div id="web-panel-qris"
                        class="mt-5 rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] p-5 text-center {{ $activeChannel === 'qris' ? '' : 'hidden' }}">
                        @if (!$qrisChannel['enable'])
                            <p class="py-6 text-xs font-bold text-[#7C6D5D]">Pembayaran QRIS sedang dinonaktifkan sementara. Silakan
                                pilih tab Transfer.</p>
                        @else
                            <p class="text-xs font-extrabold text-[#1E1915]">Scan kode {{ $qrisChannel['name'] }} di bawah ini</p>
                            <div
                                class="mx-auto mt-4 flex aspect-square w-full max-w-[300px] items-center justify-center rounded-2xl border-2 border-[#EBE1D0] bg-white p-4 shadow-xs sm:max-w-[340px]">
                                <img id="qris-payment-image" src="{{ $qrisImgSrc }}" alt="QRIS Ayamo" class="h-full w-full object-contain">
                            </div>
                            <div class="mt-3 flex justify-center">
                                <a href="{{ $qrisImgSrc }}"
                                    download="QRIS-Ayamo-{{ $order->code }}.png"
                                    data-download-qris
                                    data-qris-filename="QRIS-Ayamo-{{ $order->code }}.png"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#DFD2BD] bg-[#FAF4EA] px-4 py-2 text-xs font-black text-[#4A3B31] shadow-2xs transition hover:border-[#B83809] hover:text-[#B83809] active:scale-95">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    <span>Unduh Gambar QRIS</span>
                                </a>
                            </div>
                            <p class="mt-3 text-xs font-medium text-[#4A3B31]">
                                Masukkan nominal tepat sebesar <b
                                    class="text-[#B83809]">Rp{{ number_format($order->total, 0, ',', '.') }}</b>
                            </p>
                        @endif
                    </div>

                    @if ($waChannel['enable'])
                        <div class="mt-5">
                            <a id="web-wa-confirm-link"
                                href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($activeChannel === 'qris' ? $waMessageQris : $waMessageBank) }}"
                                target="_blank" rel="noopener"
                                class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#16A34A] text-xs font-black text-white shadow-xs transition hover:bg-[#15803D] active:scale-95">
                                <span>Konfirmasi Pembayaran via {{ $waChannel['name'] }}</span>
                                <span>↗</span>
                            </a>
                        </div>
                    @endif
                @else
                    <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">PEMBAYARAN TUNAI</span>
                    <h2 class="mt-1 font-['Outfit'] text-2xl font-black text-[#1E1915]">Bayar Langsung di Kasir</h2>
                    <p class="mt-3 text-xs leading-relaxed text-[#7C6D5D]">
                        Tunjukkan kode pesanan <b class="text-[#1E1915]">{{ $order->code }}</b> kepada kasir Ayamo saat
                        mengambil pesanan, lalu lakukan pembayaran sebesar <b
                            class="text-[#B83809]">Rp{{ number_format($order->total, 0, ',', '.') }}</b>.
                    </p>
                @endif

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <a id="web-status-link" href="{{ route('web.orders.show', $order->id) }}"
                        class="flex h-11 items-center justify-center rounded-2xl bg-[#B83809] text-xs font-black text-white shadow-2xs transition hover:bg-[#992D06] active:scale-95">
                        Lihat Status Pesanan
                    </a>
                    <a href="{{ route('web.menu') }}"
                        class="flex h-11 items-center justify-center rounded-2xl border border-[#D8CBB8] bg-white text-xs font-black text-[#1E1915] shadow-2xs transition hover:border-[#EA580C] hover:text-[#EA580C] active:scale-95">
                        Kembali ke Menu
                    </a>
                </div>
            </section>
        </div>
    </main>

    @if ($isTransfer)
        <script>
            const webWaBank = @json('https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessageBank));
            const webWaQris = @json('https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessageQris));

            function switchWebPayChannel(ch) {
                const tabBank = document.getElementById('web-tab-bank');
                const tabQris = document.getElementById('web-tab-qris');
                const panelBank = document.getElementById('web-panel-bank');
                const panelQris = document.getElementById('web-panel-qris');
                const waLink = document.getElementById('web-wa-confirm-link');
                const isQris = ch === 'qris';

                panelBank?.classList.toggle('hidden', isQris);
                panelQris?.classList.toggle('hidden', !isQris);

                if (tabBank && tabQris) {
                    tabBank.setAttribute('aria-selected', isQris ? 'false' : 'true');
                    tabQris.setAttribute('aria-selected', isQris ? 'true' : 'false');
                    tabBank.className = 'cursor-pointer rounded-xl px-4 py-2.5 font-[\'Outfit\'] text-xs font-black transition ' +
                        (!isQris ? 'bg-white text-[#B83809] shadow-2xs' : 'text-[#6B5E50] hover:text-[#1E1915]');
                    tabQris.className = 'cursor-pointer rounded-xl px-4 py-2.5 font-[\'Outfit\'] text-xs font-black transition ' +
                        (isQris ? 'bg-white text-[#B83809] shadow-2xs' : 'text-[#6B5E50] hover:text-[#1E1915]');
                }

                if (waLink) {
                    waLink.href = isQris ? webWaQris : webWaBank;
                }
            }

            async function copyWebAccount(num, btn) {
                let copied = false;
                try {
                    if (navigator.clipboard) {
                        await navigator.clipboard.writeText(num);
                        copied = true;
                    }
                } catch (e) {
                    copied = false;
                }
                window.AyamoNotify?.(copied ? 'Nomor rekening disalin!' : 'Salin nomor rekening secara manual.', copied ? 'success' : 'warning');
                if (btn && copied) {
                    const orig = btn.textContent;
                    btn.textContent = 'Disalin';
                    setTimeout(() => { btn.textContent = orig; }, 2000);
                }
            }

            document.querySelectorAll('[data-download-qris]').forEach((link) => {
                link.addEventListener('click', async (event) => {
                    const href = link.getAttribute('href');
                    const filename = link.getAttribute('data-qris-filename') || 'QRIS-Ayamo.png';
                    if (!href || !window.fetch || !window.URL?.createObjectURL) {
                        return;
                    }
                    event.preventDefault();
                    try {
                        const res = await fetch(href, { mode: 'cors' });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const blob = await res.blob();
                        const blobUrl = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(() => URL.revokeObjectURL(blobUrl), 1500);
                    } catch (e) {
                        const fallback = document.createElement('a');
                        fallback.href = href;
                        fallback.download = filename;
                        fallback.target = '_blank';
                        fallback.rel = 'noopener';
                        document.body.appendChild(fallback);
                        fallback.click();
                        fallback.remove();
                    }
                });
            });
        </script>
    @endif
@endsection
