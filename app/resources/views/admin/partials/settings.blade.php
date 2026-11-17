@php
    $channels = $paymentChannels ?? \App\Models\Settings::getPaymentChannels($settings ?? null);
    $transferRows = old('transfer_channels');
    if (! is_array($transferRows) || $transferRows === []) {
        $transferRows = $channels['transfers'];
    } else {
        $transferRows = array_values($transferRows);
    }

    $qrisEnableVal = old('payment_channel_form')
        ? in_array((string) old('qris_enable', '0'), ['1', 'true', 'on'], true)
        : (bool) ($channels['qris']['enable'] ?? true);
    $qrisNameVal = (string) old('qris_name', $channels['qris']['name'] ?? 'QRIS');
    $qrisImageVal = (string) old('qris_image', $channels['qris']['image'] ?? '');

    $waEnableVal = old('payment_channel_form')
        ? in_array((string) old('wa_enable', '0'), ['1', 'true', 'on'], true)
        : (bool) ($channels['whatsapp']['enable'] ?? true);
    $waMessageVal = (string) old('wa_message', $channels['whatsapp']['message'] ?? 'Halo, saya ingin memesan Ayamo!');

    $firstPreviewTransfer = collect($transferRows)->first(static fn ($r): bool => ! empty($r['enable'])) ?? ($transferRows[0] ?? []);
    $extraActiveCount = max(0, collect($transferRows)->filter(static fn ($r): bool => ! empty($r['enable']))->count() - 1);
@endphp

<div class="space-y-6">
    {{-- Header Banner --}}
    <section class="relative overflow-hidden rounded-[28px] border border-[#EBE1D0] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-black tracking-wide text-[#FEF08A] uppercase">
                    Konfigurasi Toko &amp; Bayar
                </span>
                <h1 class="mt-2.5 font-['Outfit'] text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Pengaturan toko
                </h1>
                <p class="mt-1 text-sm font-medium text-white/85">
                    Atur profil gerai, jam buka, daftar rekening transfer, QRIS, dan WhatsApp toko.
                </p>
            </div>

            <a href="{{ route('web.home') }}" target="_blank" rel="noopener" class="inline-flex w-fit items-center gap-1.5 rounded-2xl bg-white px-4 py-2.5 text-xs font-black text-[#B83809] shadow-xs transition hover:bg-[#FEF3C7]">
                <span>Lihat di Toko Web</span>
                <span>↗</span>
            </a>
        </div>
    </section>

    {{-- Settings Form + Live Preview --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,1fr)] xl:items-start">
        <form action="/admin/settings" method="post" enctype="multipart/form-data" class="space-y-6 rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
            @csrf
            <input type="hidden" name="payment_channel_form" value="1">

            <div>
                <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Identitas &amp; Jam Buka</h2>
                <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">
                    Tampil di header, footer, dan checkout pelanggan.
                </p>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="setting-store-name" class="block text-xs font-black text-[#4A3B31]">Nama Toko</label>
                        <input
                            id="setting-store-name"
                            name="store_name"
                            type="text"
                            required
                            maxlength="80"
                            value="{{ old('store_name', $settings['store_name'] ?? 'Ayamo') }}"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('store_name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="setting-store-time" class="block text-xs font-black text-[#4A3B31]">Jam Buka</label>
                        <input
                            id="setting-store-time"
                            name="store_time"
                            type="text"
                            required
                            maxlength="60"
                            value="{{ old('store_time', $settings['store_time'] ?? '15.00 - 03.00') }}"
                            placeholder="15.00 - 03.00"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('store_time')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4">
                    <label for="setting-store-tagline" class="block text-xs font-black text-[#4A3B31]">Tagline Toko</label>
                    <input
                        id="setting-store-tagline"
                        name="store_tagline"
                        type="text"
                        required
                        maxlength="120"
                        value="{{ old('store_tagline', $settings['store_tagline'] ?? 'Eat, Chill, Repeat') }}"
                        class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                    >
                    <x-input-error :messages="$errors->get('store_tagline')" class="mt-1" />
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="setting-phone-number" class="block text-xs font-black text-[#4A3B31]">Nomor WhatsApp / Kontak</label>
                        <input
                            id="setting-phone-number"
                            name="phone_number"
                            type="text"
                            required
                            maxlength="30"
                            value="{{ old('phone_number', $settings['phone_number'] ?? '0856-5651-6707') }}"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
                    </div>

                    <div>
                        <label for="setting-address" class="block text-xs font-black text-[#4A3B31]">Alamat Gerai</label>
                        <input
                            id="setting-address"
                            name="address"
                            type="text"
                            required
                            maxlength="250"
                            value="{{ old('address', $settings['address'] ?? '') }}"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('address')" class="mt-1" />
                    </div>
                </div>
            </div>

            <hr class="border-[#EBE1D0]">

            {{-- Transfer Channels --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Rekening Transfer &amp; Bank Digital</h2>
                        <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">
                            Semua rekening yang dicentang tampil di tab <strong>Transfer</strong> halaman bayar pelanggan.
                        </p>
                    </div>
                    <button
                        type="button"
                        id="add-transfer-channel-btn"
                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#FEF3C7] px-3.5 py-2 text-xs font-black text-[#92400E] transition hover:bg-[#FDE68A]"
                    >
                        + Tambah Rekening
                    </button>
                </div>

                <div id="transfer-channel-list" class="mt-4 space-y-3">
                    @foreach ($transferRows as $idx => $ch)
                        @php
                            $chEnabled = is_array($ch) && in_array((string) ($ch['enable'] ?? '0'), ['1', 'true', 'on'], true);
                            $chKey = (string) ($ch['key'] ?? '');
                            $chName = (string) ($ch['name'] ?? '');
                            $chNumber = (string) ($ch['number'] ?? '');
                            $chHolder = (string) ($ch['holder'] ?? '');
                        @endphp
                        <div data-transfer-row class="rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-4">
                            <input type="hidden" name="transfer_channels[{{ $idx }}][key]" value="{{ $chKey }}" data-field="key">

                            <div class="flex items-center justify-between gap-2">
                                <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-black text-[#1E1915]">
                                    <input
                                        type="checkbox"
                                        name="transfer_channels[{{ $idx }}][enable]"
                                        value="1"
                                        data-field="enable"
                                        @checked($chEnabled)
                                        class="h-4 w-4 rounded border-[#DFD2BD] text-[#B83809] focus:ring-[#B83809]"
                                    >
                                    <span>Aktif untuk pelanggan</span>
                                </label>
                                <button
                                    type="button"
                                    data-remove-transfer
                                    class="cursor-pointer rounded-lg px-2.5 py-1 text-[11px] font-black text-[#DC2626] transition hover:bg-[#FEE2E2]"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="block text-[11px] font-black text-[#4A3B31]">Bank / E-Wallet</label>
                                    <input
                                        type="text"
                                        name="transfer_channels[{{ $idx }}][name]"
                                        value="{{ $chName }}"
                                        required
                                        maxlength="100"
                                        placeholder="BSI / BCA / SeaBank / DANA"
                                        data-field="name"
                                        class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-[#4A3B31]">No. Rekening / Akun</label>
                                    <input
                                        type="text"
                                        name="transfer_channels[{{ $idx }}][number]"
                                        value="{{ $chNumber }}"
                                        required
                                        maxlength="60"
                                        placeholder="1234 5678 90"
                                        data-field="number"
                                        class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-black text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-[#4A3B31]">Atas Nama (a.n)</label>
                                    <input
                                        type="text"
                                        name="transfer_channels[{{ $idx }}][holder]"
                                        value="{{ $chHolder }}"
                                        maxlength="100"
                                        placeholder="Ayamo"
                                        data-field="holder"
                                        class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('transfer_channels')" class="mt-2" />
            </div>

            <hr class="border-[#EBE1D0]">

            {{-- QRIS Config --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Pembayaran QRIS</h2>
                        <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">
                            Ditampilkan di tab <strong>QRIS</strong> pelanggan. Upload foto QRIS atau isi URL gambarnya.
                        </p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/70 px-3 py-2 text-xs font-black text-[#1E1915]">
                        <input
                            id="setting-qris-enable"
                            type="checkbox"
                            name="qris_enable"
                            value="1"
                            @checked($qrisEnableVal)
                            class="h-4 w-4 rounded border-[#DFD2BD] text-[#B83809] focus:ring-[#B83809]"
                        >
                        <span>Aktifkan QRIS</span>
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="setting-qris-name" class="block text-xs font-black text-[#4A3B31]">Label Metode QRIS</label>
                        <input
                            id="setting-qris-name"
                            name="qris_name"
                            type="text"
                            maxlength="80"
                            value="{{ $qrisNameVal }}"
                            placeholder="QRIS Ayamo"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="setting-qris-image" class="block text-xs font-black text-[#4A3B31]">URL Gambar QRIS (opsional)</label>
                        <input
                            id="setting-qris-image"
                            name="qris_image"
                            type="text"
                            maxlength="255"
                            value="{{ $qrisImageVal }}"
                            placeholder="/img/uploads/qris.png atau https://..."
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('qris_image')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-4 rounded-2xl border border-dashed border-[#DFD2BD] bg-[#FAF4EA]/50 p-4 sm:flex-row sm:items-center">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#EBE1D0] bg-white">
                        @if ($qrisImageVal !== '')
                            <img id="preview-qris-thumb" src="{{ $qrisImageVal }}" alt="Pratinjau QRIS" class="h-full w-full object-contain p-1">
                        @else
                            <img id="preview-qris-thumb" src="" alt="Pratinjau QRIS" class="hidden h-full w-full object-contain p-1">
                            <span id="preview-qris-placeholder" class="px-2 text-center text-[10px] font-black text-[#9C8B7A]">QR Demo Default</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label for="setting-qris-file" class="block text-xs font-black text-[#4A3B31]">Upload Gambar QRIS Baru (JPG / PNG / WebP, maks 5MB)</label>
                        <input
                            id="setting-qris-file"
                            name="qris_image_file"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="mt-1.5 block w-full text-xs font-semibold text-[#5C4E41] file:mr-3 file:rounded-xl file:border-0 file:bg-[#FEF3C7] file:px-3.5 file:py-2 file:text-xs file:font-black file:text-[#92400E] hover:file:bg-[#FDE68A]"
                        >
                        <x-input-error :messages="$errors->get('qris_image_file')" class="mt-1" />
                    </div>
                </div>
            </div>

            <hr class="border-[#EBE1D0]">

            {{-- WhatsApp & Sistem --}}
            <div>
                <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">WhatsApp &amp; Akses Sistem</h2>
                <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">
                    Atur tombol konfirmasi WhatsApp, mode maintenance, dan kunci admin.
                </p>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2 flex flex-col gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-4">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-black text-[#1E1915]">
                            <input
                                type="checkbox"
                                name="wa_enable"
                                value="1"
                                @checked($waEnableVal)
                                class="h-4 w-4 rounded border-[#DFD2BD] text-[#B83809] focus:ring-[#B83809]"
                            >
                            <span>Aktifkan tombol konfirmasi WhatsApp di halaman bayar</span>
                        </label>
                        <div>
                            <label for="setting-wa-message" class="block text-[11px] font-black text-[#4A3B31]">Teks Pembuka WhatsApp</label>
                            <input
                                id="setting-wa-message"
                                name="wa_message"
                                type="text"
                                maxlength="300"
                                value="{{ $waMessageVal }}"
                                class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="setting-maintenance-mode" class="block text-xs font-black text-[#4A3B31]">Status Toko (`maintenance_mode`)</label>
                        @php $maintVal = strtolower(trim((string) old('maintenance_mode', $settings['maintenance_mode'] ?? 'false'))); @endphp
                        <select
                            id="setting-maintenance-mode"
                            name="maintenance_mode"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                            <option value="false" @selected(! in_array($maintVal, ['true', '1', 'closed', 'auto'], true))>Buka Manual (Override Buka)</option>
                            <option value="auto" @selected($maintVal === 'auto')>Ikuti Jam Operasional Otomatis</option>
                            <option value="true" @selected(in_array($maintVal, ['true', '1', 'closed'], true))>Tutup Hari Ini / Libur Sementara</option>
                        </select>
                        <x-input-error :messages="$errors->get('maintenance_mode')" class="mt-1" />
                    </div>

                    <div>
                        <label for="setting-closed-reason" class="block text-xs font-black text-[#4A3B31]">Alasan / Pengumuman Saat Tutup (opsional)</label>
                        <input
                            id="setting-closed-reason"
                            name="store_closed_reason"
                            type="text"
                            maxlength="250"
                            value="{{ old('store_closed_reason', $settings['store_closed_reason'] ?? '') }}"
                            placeholder="Contoh: Libur hari ini, buka kembali besok pukul 15.00 WIB"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('store_closed_reason')" class="mt-1" />
                    </div>

                    <div>
                        <label for="setting-admin-key" class="block text-xs font-black text-[#4A3B31]">Kunci Akses Admin (`admin_key`)</label>
                        <input
                            id="setting-admin-key"
                            name="admin_key"
                            type="text"
                            maxlength="100"
                            value="{{ old('admin_key', $settings['admin_key'] ?? 'kelompok17app') }}"
                            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                        <x-input-error :messages="$errors->get('admin_key')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-2xl bg-[#F59E0B] px-6 py-3 font-['Outfit'] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#D97706] active:scale-[0.99]"
                >
                    <span>Simpan Pengaturan Toko</span>
                    <span>→</span>
                </button>
            </div>
        </form>

        {{-- Live Preview Card --}}
        <aside class="space-y-4">
            <div class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                <span class="rounded-full bg-[#DCFCE7] px-3 py-1 text-[10px] font-black tracking-wide text-[#166534] uppercase">
                    Pratinjau Kartu Toko
                </span>
                <div class="mt-4 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA] p-4 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-['Outfit'] text-lg font-black text-[#B83809]" id="preview-store-name">
                            {{ old('store_name', $settings['store_name'] ?? 'Ayamo') }}
                        </span>
                        <span class="rounded-full bg-[#DCFCE7] px-2.5 py-0.5 text-[11px] font-black text-[#166534]" id="preview-store-time">
                            Buka {{ old('store_time', $settings['store_time'] ?? '15.00 - 03.00') }}
                        </span>
                    </div>
                    <p class="text-xs font-bold text-[#7C6D5D]" id="preview-store-tagline">
                        {{ old('store_tagline', $settings['store_tagline'] ?? 'Eat, Chill, Repeat') }}
                    </p>
                    <p class="flex items-start gap-1.5 text-xs font-semibold text-[#4A3B31]" id="preview-store-address">
                        <svg aria-hidden="true" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#B83809]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span id="preview-store-address-text">{{ old('address', $settings['address'] ?? '') }}</span>
                    </p>
                    <p class="flex items-center gap-1.5 text-xs font-semibold text-[#4A3B31]" id="preview-store-phone">
                        <svg aria-hidden="true" class="h-3.5 w-3.5 shrink-0 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span id="preview-store-phone-text">{{ old('phone_number', $settings['phone_number'] ?? '') }}</span>
                    </p>
                </div>

                <div class="mt-4 rounded-2xl border border-[#EBE1D0] bg-gradient-to-br from-[#B83809] to-[#7C2405] p-5 text-white">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] font-bold text-[#FEF08A] uppercase">Pratinjau Transfer Utama</p>
                        <span id="preview-qris-badge" class="rounded-full bg-white/15 px-2.5 py-0.5 text-[10px] font-black text-[#FEF08A]">
                            {{ $qrisEnableVal ? 'QRIS Aktif' : 'QRIS Nonaktif' }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm font-bold text-white/90" id="preview-bank-name">
                        {{ $firstPreviewTransfer['name'] ?? ($settings['bank_name'] ?? 'Bank Syariah Indonesia') }}
                    </p>
                    <p class="mt-1 font-['Outfit'] text-2xl font-black tracking-wider text-[#FEF08A]" id="preview-bank-number">
                        {{ $firstPreviewTransfer['number'] ?? ($settings['bank_account_number'] ?? '1234 5678 90') }}
                    </p>
                    <p class="mt-1 text-xs font-semibold text-white/80" id="preview-bank-holder">
                        a.n {{ $firstPreviewTransfer['holder'] ?? ($settings['bank_account_holder'] ?? 'Ayamo') }}
                    </p>
                    @if ($extraActiveCount > 0)
                        <p class="mt-2 text-[11px] font-bold text-white/85" id="preview-bank-extra">+ {{ $extraActiveCount }} rekening lain tampil di halaman bayar</p>
                    @else
                        <p class="mt-2 hidden text-[11px] font-bold text-white/85" id="preview-bank-extra"></p>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    {{-- Flexible Key-Value Manager --}}
    @php
        $kvRows = $settingRows ?? collect();
        $protectedKeys = $protectedSettingKeys ?? \App\Models\Settings::protectedKeys();
        $customRows = $kvRows->reject(static fn ($r): bool => in_array($r->key, $protectedKeys, true))->values();
        $protectedRows = $kvRows->filter(static fn ($r): bool => in_array($r->key, $protectedKeys, true))->values();
        $kvGroups = [
            ['rows' => $customRows, 'collapsed' => false],
            ['rows' => $protectedRows, 'collapsed' => true],
        ];
    @endphp
    <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs sm:p-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full bg-[#FEF3C7] px-3 py-1 text-[10px] font-black tracking-wide text-[#92400E] uppercase">
                    Key-Value Tambahan
                </span>
                <h2 class="mt-2 font-['Outfit'] text-lg font-black text-[#1E1915]">
                    Pengaturan Fleksibel (`settings`)
                </h2>
                <p class="mt-0.5 text-xs font-semibold text-[#7C6D5D]">
                    Tambah key kustom seperti teks promo, info libur, atau tautan media sosial toko.
                </p>
            </div>
            <span class="w-fit rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA] px-3.5 py-2 text-xs font-black text-[#6B5E50]">
                {{ $customRows->count() }} kustom · {{ $protectedRows->count() }} bawaan
            </span>
        </div>

        {{-- Add / Upsert Flexible Key --}}
        <form action="{{ route('admin.settings.kv') }}" method="post" class="mt-5 grid grid-cols-1 gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)_auto] lg:items-start">
            @csrf
            <div>
                <label for="kv-new-key" class="block text-xs font-black text-[#4A3B31]">Nama Key</label>
                <input
                    id="kv-new-key"
                    name="key"
                    type="text"
                    required
                    maxlength="80"
                    value="{{ old('key') }}"
                    placeholder="contoh: promo_text"
                    class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 font-mono text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                >
                <x-input-error :messages="$errors->get('key')" class="mt-1" />
            </div>

            <div>
                <label for="kv-new-value" class="block text-xs font-black text-[#4A3B31]">Nilai (Teks / Angka / JSON)</label>
                <textarea
                    id="kv-new-value"
                    name="value"
                    rows="2"
                    placeholder="contoh: Gratis kemasan kardus minimal beli 5 pcs"
                    class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-semibold text-[#1E1915] focus:border-[#B83809] focus:outline-none"
                >{{ old('value') }}</textarea>
                <x-input-error :messages="$errors->get('kv_value') ?: $errors->get('value')" class="mt-1" />
            </div>

            <div class="lg:pt-5">
                <button
                    type="submit"
                    class="w-full cursor-pointer rounded-xl bg-[#B83809] px-4 py-2.5 font-['Outfit'] text-xs font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.98] lg:w-auto"
                >
                    + Simpan Key
                </button>
            </div>
        </form>

        @foreach ($kvGroups as $kvGroup)
            @if ($kvGroup['collapsed'])
                <details class="mt-5 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/40 p-4">
                    <summary class="cursor-pointer select-none text-xs font-black text-[#5C4E41] hover:text-[#1E1915]">
                        Key Bawaan Sistem ({{ $kvGroup['rows']->count() }}) — buka untuk edit mentah atau reset ke default
                    </summary>
                    <div class="mt-4 space-y-3">
            @else
                <div class="mt-5 space-y-3">
            @endif

            @forelse ($kvGroup['rows'] as $row)
                @php
                    $isProtected = $kvGroup['collapsed'];
                    $valStr = (string) ($row->value ?? '');
                    $isMultiline = str_contains($valStr, "\n") || strlen($valStr) > 90;
                    $updatedLabel = $row->updated_at
                        ? \Illuminate\Support\Carbon::parse($row->updated_at)->format('d M Y, H:i')
                        : '-';
                @endphp
                <div class="flex flex-col gap-3 rounded-2xl border border-[#EBE1D0] bg-white p-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 lg:w-64 shrink-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <code class="rounded-lg bg-[#FAF4EA] px-2 py-0.5 font-mono text-xs font-black text-[#1E1915]">{{ $row->key }}</code>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $isProtected ? 'bg-[#E0F2FE] text-[#075985]' : 'bg-[#DCFCE7] text-[#166534]' }}">
                                {{ $isProtected ? 'Sistem' : 'Kustom' }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-[11px] font-semibold text-[#7C6D5D]">
                            Diperbarui: {{ $updatedLabel }}
                        </p>
                    </div>

                    <div class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-start">
                        <form action="{{ route('admin.settings.kv') }}" method="post" class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-start">
                            @csrf
                            <input type="hidden" name="key" value="{{ $row->key }}">
                            @if ($isMultiline)
                                <textarea
                                    name="value"
                                    rows="3"
                                    aria-label="Nilai untuk {{ $row->key }}"
                                    class="w-full rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/40 px-3 py-2 font-mono text-xs font-semibold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                                >{{ $valStr }}</textarea>
                            @else
                                <input
                                    name="value"
                                    type="text"
                                    aria-label="Nilai untuk {{ $row->key }}"
                                    value="{{ $valStr }}"
                                    class="w-full rounded-xl border border-[#DFD2BD] bg-[#FAF4EA]/40 px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
                                >
                            @endif
                            <button
                                type="submit"
                                class="shrink-0 cursor-pointer rounded-xl bg-[#FEF3C7] px-3.5 py-2 text-xs font-black text-[#92400E] transition hover:bg-[#FDE68A]"
                            >
                                Simpan
                            </button>
                        </form>

                        <form
                            action="{{ route('admin.settings.delete', ['key' => $row->key]) }}"
                            method="post"
                            data-confirm="{{ $isProtected ? 'Kembalikan key sistem `' . $row->key . '` ke nilai default?' : 'Hapus key kustom `' . $row->key . '` permanen?' }}"
                            data-confirm-title="{{ $isProtected ? 'Reset Key Sistem?' : 'Hapus Key Pengaturan?' }}"
                            data-confirm-button="{{ $isProtected ? 'Ya, Reset' : 'Ya, Hapus' }}"
                            data-confirm-variant="{{ $isProtected ? 'warning' : 'danger' }}"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="w-full shrink-0 cursor-pointer rounded-xl px-3.5 py-2 text-xs font-black transition sm:w-auto {{ $isProtected ? 'bg-[#F3F4F6] text-[#4B5563] hover:bg-[#E5E7EB]' : 'bg-[#FEE2E2] text-[#DC2626] hover:bg-[#FECACA]' }}"
                            >
                                {{ $isProtected ? 'Reset' : 'Hapus' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-[#DFD2BD] bg-[#FAF4EA]/40 p-5 text-center text-xs font-bold text-[#7C6D5D]">
                    Belum ada key kustom. Tambahkan di form atas jika diperlukan.
                </div>
            @endforelse

            @if ($kvGroup['collapsed'])
                    </div>
                </details>
            @else
                </div>
            @endif
        @endforeach
    </section>
</div>

<script>
(function () {
    const bindings = [
        ['setting-store-name', 'preview-store-name', (v) => v || 'Ayamo'],
        ['setting-store-time', 'preview-store-time', (v) => 'Buka ' + (v || '15.00 - 03.00')],
        ['setting-store-tagline', 'preview-store-tagline', (v) => v || 'Eat, Chill, Repeat'],
        ['setting-address', 'preview-store-address-text', (v) => v || '-'],
        ['setting-phone-number', 'preview-store-phone-text', (v) => v || '-'],
    ];

    const maintSelect = document.getElementById('setting-maintenance-mode');
    const previewTimeEl = document.getElementById('preview-store-time');
    function syncStoreStatusPreview() {
        if (!maintSelect || !previewTimeEl) return;
        const timeVal = document.getElementById('setting-store-time')?.value?.trim() || '15.00 - 03.00';
        if (maintSelect.value === 'true') {
            previewTimeEl.textContent = 'Tutup Hari Ini';
            previewTimeEl.className = 'rounded-full bg-[#FEE2E2] px-2.5 py-0.5 text-[11px] font-black text-[#991B1B]';
        } else if (maintSelect.value === 'auto') {
            previewTimeEl.textContent = 'Jadwal ' + timeVal;
            previewTimeEl.className = 'rounded-full bg-[#FEF3C7] px-2.5 py-0.5 text-[11px] font-black text-[#92400E]';
        } else {
            previewTimeEl.textContent = 'Buka ' + timeVal;
            previewTimeEl.className = 'rounded-full bg-[#DCFCE7] px-2.5 py-0.5 text-[11px] font-black text-[#166534]';
        }
    }
    if (maintSelect) {
        maintSelect.addEventListener('change', syncStoreStatusPreview);
        syncStoreStatusPreview();
    }

    bindings.forEach(([inputId, targetId, format]) => {
        const input = document.getElementById(inputId);
        const target = document.getElementById(targetId);
        if (!input || !target) return;
        input.addEventListener('input', () => {
            target.textContent = format(input.value.trim());
        });
    });

    const listEl = document.getElementById('transfer-channel-list');
    const addBtn = document.getElementById('add-transfer-channel-btn');

    function reindexTransferRows() {
        if (!listEl) return;
        listEl.querySelectorAll('[data-transfer-row]').forEach((row, idx) => {
            row.querySelectorAll('[data-field]').forEach((fieldEl) => {
                const field = fieldEl.getAttribute('data-field');
                if (field) {
                    fieldEl.setAttribute('name', `transfer_channels[${idx}][${field}]`);
                }
            });
        });
    }

    function syncBankPreview() {
        if (!listEl) return;
        const rows = Array.from(listEl.querySelectorAll('[data-transfer-row]'));
        const activeRows = rows.filter((row) => row.querySelector('[data-field="enable"]')?.checked);
        const primaryRow = activeRows[0] || rows[0];
        if (!primaryRow) return;

        const nameVal = primaryRow.querySelector('[data-field="name"]')?.value?.trim() || 'Bank Syariah Indonesia';
        const numVal = primaryRow.querySelector('[data-field="number"]')?.value?.trim() || '-';
        const holderVal = primaryRow.querySelector('[data-field="holder"]')?.value?.trim() || 'Ayamo';

        const pName = document.getElementById('preview-bank-name');
        const pNum = document.getElementById('preview-bank-number');
        const pHolder = document.getElementById('preview-bank-holder');
        const pExtra = document.getElementById('preview-bank-extra');

        if (pName) pName.textContent = nameVal;
        if (pNum) pNum.textContent = numVal;
        if (pHolder) pHolder.textContent = 'a.n ' + holderVal;
        if (pExtra) {
            const extra = Math.max(0, activeRows.length - 1);
            pExtra.textContent = extra > 0 ? `+ ${extra} rekening lain tampil di halaman bayar` : '';
            pExtra.classList.toggle('hidden', extra === 0);
        }
    }

    if (listEl) {
        listEl.addEventListener('input', syncBankPreview);
        listEl.addEventListener('change', syncBankPreview);
        listEl.addEventListener('click', (event) => {
            const removeBtn = event.target.closest('[data-remove-transfer]');
            if (!removeBtn) return;
            const rows = listEl.querySelectorAll('[data-transfer-row]');
            if (rows.length <= 1) {
                if (typeof window.AyamoAlert === 'function') {
                    window.AyamoAlert('Minimal satu rekening transfer wajib tersedia.');
                }
                return;
            }
            removeBtn.closest('[data-transfer-row]')?.remove();
            reindexTransferRows();
            syncBankPreview();
        });
    }

    if (addBtn && listEl) {
        addBtn.addEventListener('click', () => {
            const idx = listEl.querySelectorAll('[data-transfer-row]').length;
            const storeName = document.getElementById('setting-store-name')?.value?.trim() || 'Ayamo';
            const row = document.createElement('div');
            row.setAttribute('data-transfer-row', '');
            row.className = 'rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-4';
            row.innerHTML = `
                <input type="hidden" name="transfer_channels[${idx}][key]" value="" data-field="key">
                <div class="flex items-center justify-between gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-black text-[#1E1915]">
                        <input type="checkbox" name="transfer_channels[${idx}][enable]" value="1" data-field="enable" checked class="h-4 w-4 rounded border-[#DFD2BD] text-[#B83809] focus:ring-[#B83809]">
                        <span>Aktif untuk pelanggan</span>
                    </label>
                    <button type="button" data-remove-transfer class="cursor-pointer rounded-lg px-2.5 py-1 text-[11px] font-black text-[#DC2626] transition hover:bg-[#FEE2E2]">Hapus</button>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <label class="block text-[11px] font-black text-[#4A3B31]">Bank / E-Wallet</label>
                        <input type="text" name="transfer_channels[${idx}][name]" value="" required maxlength="100" placeholder="BCA / SeaBank / DANA" data-field="name" class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-[#4A3B31]">No. Rekening / Akun</label>
                        <input type="text" name="transfer_channels[${idx}][number]" value="" required maxlength="60" placeholder="9012 3456 78" data-field="number" class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-black text-[#1E1915] focus:border-[#B83809] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-[#4A3B31]">Atas Nama (a.n)</label>
                        <input type="text" name="transfer_channels[${idx}][holder]" value="${storeName.replace(/"/g, '&quot;')}" maxlength="100" placeholder="Ayamo" data-field="holder" class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-2 text-xs font-bold text-[#1E1915] focus:border-[#B83809] focus:outline-none">
                    </div>
                </div>
            `;
            listEl.appendChild(row);
            row.querySelector('[data-field="name"]')?.focus();
            syncBankPreview();
        });
    }

    const qrisCheck = document.getElementById('setting-qris-enable');
    const qrisBadge = document.getElementById('preview-qris-badge');
    if (qrisCheck && qrisBadge) {
        qrisCheck.addEventListener('change', () => {
            qrisBadge.textContent = qrisCheck.checked ? 'QRIS Aktif' : 'QRIS Nonaktif';
        });
    }

    const qrisUrlInput = document.getElementById('setting-qris-image');
    const qrisFileInput = document.getElementById('setting-qris-file');
    const qrisThumb = document.getElementById('preview-qris-thumb');
    const qrisPlaceholder = document.getElementById('preview-qris-placeholder');
    const setQrisThumb = (src) => {
        if (!qrisThumb) return;
        if (src) {
            qrisThumb.src = src;
            qrisThumb.classList.remove('hidden');
            qrisPlaceholder?.classList.add('hidden');
        } else {
            qrisThumb.classList.add('hidden');
            qrisPlaceholder?.classList.remove('hidden');
        }
    };

    if (qrisUrlInput) {
        qrisUrlInput.addEventListener('input', () => setQrisThumb(qrisUrlInput.value.trim()));
    }
    if (qrisFileInput) {
        qrisFileInput.addEventListener('change', () => {
            const file = qrisFileInput.files?.[0];
            if (file) {
                setQrisThumb(URL.createObjectURL(file));
            }
        });
    }
})();
</script>

