@extends('layouts.mobile')
@section('title', 'Status Pesanan | Ayamo Mobile')
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

        $stepMap = [
            'pending' => 1,
            'processing' => 2,
            'ready' => 3,
            'confirmed' => 4,
            'completed' => 4,
        ];
        $currentStep = $stepMap[$order->status] ?? 1;

        $waConfirmMsg = "Halo Ayamo, saya ingin konfirmasi pesanan {$order->code}.\n\nTotal: Rp" . number_format($order->total, 0, ',', '.') . "\nMetode pembayaran: " . ($activeChannel === 'qris' ? 'QRIS' : 'Transfer Bank') . "\n\nBerikut bukti transaksinya. Mohon diproses.";
        $waIssueMsg = "Hai, saya mengalami kendala terkait pesanan dengan kode {$order->code}.\n\nDeskripsi kendala:\n....";
    @endphp

    <main class="flex flex-1 flex-col justify-between px-4 pt-3 pb-12">
        <div>
            {{-- Top Bar --}}
            <header class="relative mb-5 flex items-center justify-between pt-1">
                <a href="{{ route('mobile.history') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-[#CBE0E8] bg-[#F3F8FA] text-[#1E1915] shadow-2xs transition hover:bg-[#E5F1F6] active:scale-95"
                    aria-label="Kembali ke Riwayat">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="flex-1 px-3 text-center font-['Outfit'] text-xl font-extrabold text-[#1E1915] truncate">
                    Status Pesanan
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

            {{-- Card 2: Info Pembayaran / Lupa Bayar Accordion --}}
            @if (!$isTransfer)
                <section class="mb-4 rounded-[20px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] px-4 py-3">
                    <p class="text-xs leading-relaxed font-medium text-[#1E1915]">
                        Tunjukkan kode pesanan kepada kasir untuk melakukan pembayaran.
                    </p>
                </section>
            @else
                <section class="mb-4 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-3.5">
                    <button type="button" onclick="toggleForgotPay()"
                        class="flex w-full cursor-pointer items-center justify-between px-1 text-left">
                        <span class="font-['Outfit'] text-xs font-extrabold text-[#1E1915]">Lupa bayar? klik disini</span>
                        <span id="forgot-pay-chevron" class="text-sm font-extrabold text-[#1E1915] transition-transform">{{ request()->has('channel') ? '<' : '>' }}</span>
                    </button>

                    <div id="forgot-pay-body" style="{{ request()->has('channel') ? '' : 'display: none;' }}" class="mt-3 space-y-3">
                        {{-- Tab Switcher Bank | QRIS --}}
                        <div class="grid grid-cols-2 gap-2 rounded-2xl border-[1.4px] border-[#3D312A] bg-[#F5EFE2] p-1.5">
                            <button type="button" id="status-tab-bank" onclick="switchStatusChannel('bank')"
                                class="cursor-pointer rounded-xl bg-white py-2 font-['Outfit'] text-xs font-extrabold transition {{ $activeChannel === 'bank' ? 'text-[#B83809] shadow-2xs' : 'text-[#1E1915]' }}">
                                Bank
                            </button>
                            <button type="button" id="status-tab-qris" onclick="switchStatusChannel('qris')"
                                class="cursor-pointer rounded-xl bg-white py-2 font-['Outfit'] text-xs font-extrabold transition {{ $activeChannel === 'qris' ? 'text-[#B83809] shadow-2xs' : 'text-[#1E1915]' }}">
                                QRIS
                            </button>
                        </div>

                        {{-- Sub-card Bank --}}
                        <div id="status-panel-bank"
                            style="{{ $activeChannel === 'bank' ? '' : 'display: none;' }}"
                            class="rounded-2xl border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4">
                            <h3 class="font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Transfer Bank</h3>
                            <p class="mt-1.5 text-xs font-medium text-[#1E1915]">{{ $bankName }}</p>
                            <p class="mt-1.5 text-center font-['Outfit'] text-base font-extrabold text-[#B83809]">{{ $bankNumber }}</p>
                            <p class="mb-3 text-center text-xs font-medium text-[#1E1915]">a.n {{ $bankHolder }}</p>
                            <button type="button" id="status-copy-btn"
                                onclick="copyStatusBank('{{ preg_replace('/\s+/', '', $bankNumber) }}')"
                                class="mx-auto block w-full cursor-pointer rounded-full border-[1.4px] border-[#3D312A] bg-[#FAF6EA] py-1.5 text-xs font-extrabold text-[#1E1915] active:scale-95">
                                Salin nomor rekening
                            </button>
                        </div>

                        {{-- Sub-card QRIS --}}
                        <div id="status-panel-qris"
                            style="{{ $activeChannel === 'qris' ? '' : 'display: none;' }}"
                            class="rounded-2xl border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4">
                            <h3 class="mb-2 font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Scan QRIS</h3>
                            <div class="mx-auto flex h-36 w-36 items-center justify-center rounded-xl bg-white p-2.5">
                                <svg viewBox="0 0 21 21" class="h-full w-full text-[#1E1915]" fill="currentColor" shape-rendering="crispEdges">
                                    <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill-rule="evenodd"/>
                                    <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill-rule="evenodd"/>
                                    <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill-rule="evenodd"/>
                                    <rect x="8" y="0" width="1" height="2"/><rect x="10" y="0" width="2" height="1"/>
                                    <rect x="9" y="2" width="3" height="1"/><rect x="8" y="4" width="2" height="2"/>
                                    <rect x="11" y="4" width="2" height="1"/><rect x="12" y="5" width="1" height="2"/>
                                    <rect x="0" y="8" width="2" height="1"/><rect x="3" y="8" width="2" height="2"/>
                                    <rect x="6" y="8" width="3" height="1"/><rect x="10" y="8" width="2" height="3"/>
                                    <rect x="13" y="8" width="3" height="1"/><rect x="17" y="8" width="2" height="2"/>
                                    <rect x="1" y="10" width="2" height="2"/><rect x="5" y="10" width="4" height="1"/>
                                    <rect x="12" y="10" width="2" height="2"/><rect x="15" y="10" width="2" height="1"/>
                                    <rect x="3" y="12" width="3" height="1"/><rect x="7" y="12" width="4" height="1"/>
                                    <rect x="14" y="12" width="3" height="2"/><rect x="8" y="14" width="2" height="2"/>
                                    <rect x="11" y="14" width="2" height="1"/><rect x="18" y="14" width="2" height="2"/>
                                    <rect x="9" y="16" width="4" height="2"/><rect x="14" y="15" width="3" height="2"/>
                                    <rect x="8" y="19" width="3" height="2"/><rect x="12" y="18" width="2" height="3"/>
                                    <rect x="15" y="18" width="3" height="1"/><rect x="19" y="17" width="2" height="3"/>
                                </svg>
                            </div>
                            <p class="mt-2 text-center text-[11px] leading-relaxed font-medium text-[#1E1915]">
                                QRIS: Masukkan dengan nominal yang tepat<br>
                                sebesar Rp. {{ number_format($order->total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Card 3: Order Status Stepper Timeline --}}
            @php
                $steps = [
                    ['num' => '1', 'label' => 'Menunggu konfirmasi', 'active' => $currentStep >= 1],
                    ['num' => '2', 'label' => 'Diproses', 'active' => $currentStep >= 2],
                    ['num' => '3', 'label' => 'Siap diambil', 'active' => $currentStep >= 3],
                    ['num' => '4', 'label' => 'Selesai', 'active' => $currentStep >= 4],
                ];
            @endphp
            <section class="mb-5 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-5">
                <div class="relative space-y-5">
                    {{-- Vertical Line --}}
                    <div class="absolute top-4 bottom-4 left-[17px] w-[2px] bg-[#F5DD90]"></div>

                    @foreach ($steps as $idx => $step)
                        <div class="relative z-10 flex items-center gap-4">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-extrabold text-[#1E1915] {{ $step['active'] ? 'bg-[#F3B825]' : 'bg-[#FBF0C4]' }}">
                                {{ $step['num'] }}
                            </span>
                            <span class="font-['Outfit'] text-sm font-extrabold text-[#1E1915]">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Action Buttons --}}
            <div class="space-y-3">
                <a href="{{ route('mobile.orders.status', ['code' => $order->id, 'channel' => $activeChannel]) }}"
                    class="block w-full rounded-2xl border-[1.4px] border-[#B83809] bg-[#FFFDF9] py-3.5 text-center font-['Outfit'] text-sm font-bold text-[#B83809] transition hover:bg-[#FFF5EE] active:scale-[0.99]">
                    Perbarui Status
                </a>

                @if ($isTransfer && !request()->has('channel'))
                    <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waConfirmMsg) }}"
                        target="_blank" rel="noopener"
                        class="block w-full rounded-full border-[1.4px] border-[#3D312A] bg-[#B83809] py-3.5 text-center font-['Outfit'] text-sm font-bold text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.99]">
                        Konfirmasi via WhatsApp
                    </a>
                @endif
            </div>
        </div>

        <div>
            {{-- Bottom Support Link (Bantuan - Kendala) --}}
            <div class="mt-8 text-center">
                <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waIssueMsg) }}"
                    target="_blank" rel="noopener"
                    class="text-xs font-medium text-[#9A948C] transition hover:text-[#B83809]">
                    Butuh bantuan? Silahkan hubungi kami
                </a>
            </div>
        </div>
    </main>

    <script>
        function toggleForgotPay() {
            const body = document.getElementById('forgot-pay-body');
            const chev = document.getElementById('forgot-pay-chevron');
            if (!body) return;
            const isHidden = body.style.display === 'none';
            body.style.display = isHidden ? 'block' : 'none';
            if (chev) chev.innerHTML = isHidden ? '&lt;' : '&gt;';
        }

        function switchStatusChannel(ch) {
            const btnBank = document.getElementById('status-tab-bank');
            const btnQris = document.getElementById('status-tab-qris');
            const panelBank = document.getElementById('status-panel-bank');
            const panelQris = document.getElementById('status-panel-qris');
            if (ch === 'qris') {
                panelBank.style.display = 'none';
                panelQris.style.display = 'block';
                btnQris.classList.add('text-[#B83809]', 'shadow-2xs');
                btnQris.classList.remove('text-[#1E1915]');
                btnBank.classList.remove('text-[#B83809]', 'shadow-2xs');
                btnBank.classList.add('text-[#1E1915]');
            } else {
                panelQris.style.display = 'none';
                panelBank.style.display = 'block';
                btnBank.classList.add('text-[#B83809]', 'shadow-2xs');
                btnBank.classList.remove('text-[#1E1915]');
                btnQris.classList.remove('text-[#B83809]', 'shadow-2xs');
                btnQris.classList.add('text-[#1E1915]');
            }
        }

        async function copyStatusBank(num) {
            const btn = document.getElementById('status-copy-btn');
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
