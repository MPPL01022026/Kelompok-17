@extends('layouts.mobile')
@section('title', 'Pembayaran | Ayamo Mobile')
@section('content')
    @php
        $isTransfer = in_array($order->payment_method, ['whatsapp', 'transfer'], true);
        $activeChannel = in_array($channel ?? 'bank', ['bank', 'qris'], true) ? ($channel ?? 'bank') : 'bank';
        $waPhone = preg_replace('/[^0-9]/', '', $settings['phone_number'] ?? '62895346200506');
        if (str_starts_with($waPhone, '0')) {
            $waPhone = '62' . substr($waPhone, 1);
        }
        $bankName = $settings['bank_name'] ?? 'Bank Syariah Indonesia';
        $bankNumber = $settings['bank_account_number'] ?? '1234 5678 90';
        $bankHolder = $settings['bank_account_holder'] ?? 'Ayamo';

        $waMessageBank = "Halo Ayamo, saya ingin konfirmasi pesanan {$order->code}.\n\nTotal: Rp" . number_format($order->total, 0, ',', '.') . "\nMetode pembayaran: Transfer Bank\n\nBerikut bukti transaksinya. Mohon diproses.";
        $waMessageQris = "Halo Ayamo, saya ingin konfirmasi pesanan {$order->code}.\n\nTotal: Rp" . number_format($order->total, 0, ',', '.') . "\nMetode pembayaran: QRIS\n\nBerikut bukti transaksinya. Mohon diproses.";
    @endphp

    <main class="flex flex-1 flex-col justify-between px-4 pt-3 pb-12">
        <div>
            {{-- Top Bar --}}
            <header class="relative mb-5 flex items-center justify-between pt-1">
                <a href="{{ route('mobile.cart') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-[#CBE0E8] bg-[#F3F8FA] text-[#1E1915] shadow-2xs transition hover:bg-[#E5F1F6] active:scale-95"
                    aria-label="Kembali ke Keranjang">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="flex-1 px-3 text-center font-['Outfit'] text-xl font-extrabold text-[#1E1915] truncate">
                    Pembayaran
                </h1>
                <div class="h-10 w-10 shrink-0"></div>
            </header>

            {{-- Card 1: Kode Pesanan & Total Bayar --}}
            <section class="mb-4 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4">
                <p class="text-center text-xs font-medium text-[#3D312A]">Kode pesanan</p>
                <p class="my-2 text-center font-['Outfit'] text-xl font-extrabold text-[#B83809]">{{ $order->code }}</p>
                <div class="my-3 border-b border-dotted border-[#3D312A]"></div>
                <div class="flex items-center justify-between font-['Outfit'] text-base font-extrabold text-[#1E1915]">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </section>

            @if (!$isTransfer)
                {{-- Metode Tunai (Pembayaran - metode tunai.png) --}}
                <section class="mb-5 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4">
                    <h2 class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">Bayar tunai</h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-[#2C241D]">
                        Tunjukan kode pesanan kasir, lalu bayar {{ number_format($order->total, 0, ',', '.') }}
                    </p>
                </section>

                <a href="{{ route('mobile.orders.status', $order->id) }}"
                    class="block w-full rounded-full border-[1.4px] border-[#3D312A] bg-[#B83809] py-3.5 text-center font-['Outfit'] text-sm font-bold text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.99]">
                    Lihat status pesanan
                </a>
            @else
                {{-- Metode Transfer: Tab Switcher Bank | QRIS --}}
                <div class="mb-4 grid grid-cols-2 gap-2.5 rounded-[20px] border-[1.4px] border-[#3D312A] bg-[#F5EFE2] p-1.5">
                    <button type="button" id="tab-btn-bank" onclick="switchPaymentChannel('bank')"
                        class="cursor-pointer rounded-2xl bg-white py-2.5 font-['Outfit'] text-sm font-extrabold transition {{ $activeChannel === 'bank' ? 'text-[#B83809] shadow-2xs' : 'text-[#1E1915]' }}">
                        Bank
                    </button>
                    <button type="button" id="tab-btn-qris" onclick="switchPaymentChannel('qris')"
                        class="cursor-pointer rounded-2xl bg-white py-2.5 font-['Outfit'] text-sm font-extrabold transition {{ $activeChannel === 'qris' ? 'text-[#B83809] shadow-2xs' : 'text-[#1E1915]' }}">
                        QRIS
                    </button>
                </div>

                {{-- Panel Bank (Pembayaran - metode bank.png) --}}
                <section id="panel-bank" style="{{ $activeChannel === 'bank' ? '' : 'display: none;' }}"
                    class="mb-6 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-5">
                    <h2 class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">Transfer Bank</h2>
                    <p class="mt-2 text-xs font-medium text-[#1E1915]">{{ $bankName }}</p>
                    <p class="mt-2 text-center font-['Outfit'] text-lg font-extrabold text-[#B83809]">{{ $bankNumber }}</p>
                    <p class="mt-0.5 mb-4 text-center text-xs font-medium text-[#1E1915]">a.n {{ $bankHolder }}</p>

                    <button type="button" onclick="copyBankNumber('{{ preg_replace('/\s+/', '', $bankNumber) }}')"
                        id="copy-bank-btn"
                        class="mx-auto block w-full max-w-[260px] cursor-pointer rounded-full border-[1.4px] border-[#3D312A] bg-[#FAF6EA] py-2 text-xs font-extrabold text-[#1E1915] transition hover:bg-[#F2E8D0] active:scale-95">
                        Salin nomor rekening
                    </button>
                </section>

                {{-- Panel QRIS (Pembayaran - metode qris.png) --}}
                <section id="panel-qris" style="{{ $activeChannel === 'qris' ? '' : 'display: none;' }}"
                    class="mb-6 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-5">
                    <h2 class="mb-3 font-['Outfit'] text-base font-extrabold text-[#1E1915]">Scan QRIS</h2>

                    <div class="mx-auto flex h-44 w-44 items-center justify-center rounded-xl bg-white p-3 shadow-2xs">
                        <svg viewBox="0 0 21 21" class="h-full w-full text-[#1E1915]" fill="currentColor"
                            shape-rendering="crispEdges">
                            <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill-rule="evenodd" />
                            <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill-rule="evenodd" />
                            <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill-rule="evenodd" />
                            <rect x="8" y="0" width="1" height="2" />
                            <rect x="10" y="0" width="2" height="1" />
                            <rect x="9" y="2" width="3" height="1" />
                            <rect x="8" y="4" width="2" height="2" />
                            <rect x="11" y="4" width="2" height="1" />
                            <rect x="12" y="5" width="1" height="2" />
                            <rect x="0" y="8" width="2" height="1" />
                            <rect x="3" y="8" width="2" height="2" />
                            <rect x="6" y="8" width="3" height="1" />
                            <rect x="10" y="8" width="2" height="3" />
                            <rect x="13" y="8" width="3" height="1" />
                            <rect x="17" y="8" width="2" height="2" />
                            <rect x="20" y="8" width="1" height="2" />
                            <rect x="1" y="10" width="2" height="2" />
                            <rect x="5" y="10" width="4" height="1" />
                            <rect x="12" y="10" width="2" height="2" />
                            <rect x="15" y="10" width="2" height="1" />
                            <rect x="18" y="11" width="3" height="1" />
                            <rect x="0" y="12" width="1" height="1" />
                            <rect x="3" y="12" width="3" height="1" />
                            <rect x="7" y="12" width="4" height="1" />
                            <rect x="14" y="12" width="3" height="2" />
                            <rect x="8" y="14" width="2" height="2" />
                            <rect x="11" y="14" width="2" height="1" />
                            <rect x="18" y="14" width="2" height="2" />
                            <rect x="9" y="16" width="4" height="2" />
                            <rect x="14" y="15" width="3" height="2" />
                            <rect x="8" y="19" width="3" height="2" />
                            <rect x="12" y="18" width="2" height="3" />
                            <rect x="15" y="18" width="3" height="1" />
                            <rect x="19" y="17" width="2" height="3" />
                            <rect x="16" y="20" width="2" height="1" />
                        </svg>
                    </div>

                    <p class="mt-3 text-center text-xs leading-relaxed font-medium text-[#1E1915]">
                        QRIS: Masukkan dengan nominal yang tepat<br>
                        sebesar Rp. {{ number_format($order->total, 0, ',', '.') }}
                    </p>
                </section>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <a id="wa-confirm-link"
                        href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($activeChannel === 'qris' ? $waMessageQris : $waMessageBank) }}"
                        target="_blank" rel="noopener"
                        class="block w-full rounded-full border-[1.4px] border-[#3D312A] bg-[#B83809] py-3.5 text-center font-['Outfit'] text-sm font-bold text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.99]">
                        Konfirmasi via WhatsApp
                    </a>

                    <a id="status-page-link" href="{{ route('mobile.orders.status', ['code' => $order->id, 'channel' => $activeChannel]) }}"
                        class="block w-full rounded-full border-[1.4px] border-[#B83809] bg-[#FFFDF9] py-3.5 text-center font-['Outfit'] text-sm font-bold text-[#B83809] transition hover:bg-[#FFF5EE] active:scale-[0.99]">
                        Lihat status pesanan
                    </a>
                </div>

                <p class="mt-3 text-center text-[11px] font-medium text-[#3D312A]">
                    Pesanan diproses setelah admin memverifikasi pembayaran .
                </p>
            @endif
        </div>
    </main>

    <script>
        const waUrlBank = @json('https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessageBank));
        const waUrlQris = @json('https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessageQris));
        const statusBaseUrl = @json(route('mobile.orders.status', $order->id));

        function switchPaymentChannel(ch) {
            const btnBank = document.getElementById('tab-btn-bank');
            const btnQris = document.getElementById('tab-btn-qris');
            const panelBank = document.getElementById('panel-bank');
            const panelQris = document.getElementById('panel-qris');
            const waLink = document.getElementById('wa-confirm-link');
            const statusLink = document.getElementById('status-page-link');

            if (ch === 'qris') {
                panelBank.style.display = 'none';
                panelQris.style.display = 'block';
                btnQris.classList.add('text-[#B83809]', 'shadow-2xs');
                btnQris.classList.remove('text-[#1E1915]');
                btnBank.classList.remove('text-[#B83809]', 'shadow-2xs');
                btnBank.classList.add('text-[#1E1915]');
                if (waLink) waLink.href = waUrlQris;
                if (statusLink) statusLink.href = statusBaseUrl + '?channel=qris';
            } else {
                panelQris.style.display = 'none';
                panelBank.style.display = 'block';
                btnBank.classList.add('text-[#B83809]', 'shadow-2xs');
                btnBank.classList.remove('text-[#1E1915]');
                btnQris.classList.remove('text-[#B83809]', 'shadow-2xs');
                btnQris.classList.add('text-[#1E1915]');
                if (waLink) waLink.href = waUrlBank;
                if (statusLink) statusLink.href = statusBaseUrl + '?channel=bank';
            }
        }

        async function copyBankNumber(num) {
            const btn = document.getElementById('copy-bank-btn');
            let copied = false;

            try {
                if (navigator.clipboard) {
                    await navigator.clipboard.writeText(num);
                    copied = true;
                }
            } catch (error) {
                copied = false;
            }

            if (typeof showMobileToast === 'function') {
                showMobileToast(copied ? 'Nomor rekening berhasil disalin!' : 'Salin nomor rekening secara manual.', !copied);
            }

            if (btn && copied) {
                const orig = btn.innerText;
                btn.innerText = 'Nomor rekening disalin! ✓';
                setTimeout(() => { btn.innerText = orig; }, 2000);
            }
        }
    </script>
@endsection
