@extends('layouts.web')
@section('title', 'Instruksi Pembayaran ' . $order->code . ' — Ayamo')

@section('content')
    @php
        $isTransfer = in_array($order->payment_method, ['whatsapp', 'transfer'], true);
        $waPhone = preg_replace('/[^0-9]/', '', $settings['phone_number'] ?? '62895346200506');
        if (str_starts_with($waPhone, '0')) {
            $waPhone = '62' . substr($waPhone, 1);
        }
        $bankName = $settings['bank_name'] ?? 'Bank Syariah Indonesia';
        $bankNumber = $settings['bank_account_number'] ?? '1234 5678 90';
        $bankHolder = $settings['bank_account_holder'] ?? 'Ayamo';
        $waMessage = "Halo Ayamo, saya ingin konfirmasi pembayaran pesanan {$order->code}. Total: Rp" . number_format($order->total, 0, ',', '.') . ". Berikut bukti pembayarannya.";
    @endphp

    <main class="mx-auto min-h-[65vh] max-w-5xl px-4 pt-10 pb-20 sm:px-6 sm:pt-12 lg:px-8 lg:pt-14">
        {{-- Success Banner --}}
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-[#DCFCE7] bg-[#F0FDF4] px-4 py-1.5 text-xs font-black text-[#166534] shadow-2xs">
                <span>✓</span>
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
                        Status pesanan: <b class="text-[#1E1915]">Menunggu Konfirmasi</b>. Admin outlet akan segera menyiapkan pesananmu setelah pembayaran terverifikasi.
                    </p>
                </div>
            </section>

            {{-- Right Card: Instruksi Pembayaran --}}
            <section class="rounded-[2.2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
                @if ($isTransfer)
                    <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">TRANSFER PEMBAYARAN</span>
                    <h2 class="mt-1 font-['Outfit'] text-2xl font-black text-[#1E1915]">{{ $bankName }}</h2>

                    <div class="mt-4 rounded-2xl border border-[#EBE1D0] bg-[#FAF5EB] p-5">
                        <p class="text-xs font-bold text-[#8C7E70]">Nomor Rekening</p>
                        <p class="mt-1 font-['Outfit'] text-2xl font-black tracking-wide text-[#B83809]">{{ $bankNumber }}</p>
                        <p class="mt-0.5 text-xs font-semibold text-[#1E1915]">a.n {{ $bankHolder }}</p>

                        <button type="button" onclick="navigator.clipboard?.writeText('{{ preg_replace('/\s+/', '', $bankNumber) }}'); this.innerText='Nomor Rekening Disalin! ✓'; setTimeout(()=>this.innerText='Salin Nomor Rekening', 2000)"
                            class="mt-3 cursor-pointer rounded-full border border-[#D8CBB8] bg-white px-4 py-1.5 text-xs font-extrabold text-[#1E1915] shadow-2xs transition hover:bg-[#FAF4EA] active:scale-95">
                            Salin Nomor Rekening
                        </button>
                    </div>

                    <div class="mt-5">
                        <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waMessage) }}"
                            target="_blank" rel="noopener"
                            class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#16A34A] text-xs font-black text-white shadow-xs transition hover:bg-[#15803D] active:scale-95">
                            <span>💬 Konfirmasi via WhatsApp Outlet</span>
                        </a>
                    </div>
                @else
                    <span class="text-xs font-black tracking-widest text-[#EA580C] uppercase">PEMBAYARAN TUNAI</span>
                    <h2 class="mt-1 font-['Outfit'] text-2xl font-black text-[#1E1915]">Bayar Langsung di Kasir</h2>
                    <p class="mt-3 text-xs leading-relaxed text-[#7C6D5D]">
                        Tunjukkan kode pesanan <b class="text-[#1E1915]">{{ $order->code }}</b> kepada kasir Ayamo saat mengambil pesanan, lalu lakukan pembayaran sebesar <b class="text-[#B83809]">Rp{{ number_format($order->total, 0, ',', '.') }}</b>.
                    </p>
                @endif

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('web.orders.show', $order->id) }}"
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
@endsection
