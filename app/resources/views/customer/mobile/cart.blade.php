@extends('layouts.mobile')
@section('title', 'Keranjang | Ayamo Mobile')
@section('content')
    @php
        $catalogJson = $products->mapWithKeys(fn ($p) => [
            $p->id => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (int) $p->price,
                'stock' => (int) $p->stock,
            ],
        ]);
        $selectedMethod = old('payment_method', request('method', 'cod'));
    @endphp

    <main class="flex flex-1 flex-col px-4 pt-3 pb-12">
        {{-- Top Bar with Back Button and Title --}}
        <header class="relative mb-5 flex items-center justify-between pt-1">
            <a href="{{ route('mobile.menu') }}"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-[#CBE0E8] bg-[#F3F8FA] text-[#1E1915] shadow-2xs transition hover:bg-[#E5F1F6] active:scale-95"
                aria-label="Kembali ke Menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="flex-1 px-3 text-center font-['Outfit'] text-xl font-extrabold text-[#1E1915] truncate">
                Keranjang
            </h1>
            <div class="h-10 w-10 shrink-0"></div>
        </header>

        {{-- Empty Cart State (Toggled dynamically by JS) --}}
        <div id="cart-empty-state" class="hidden rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-8 text-center">
            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#FBF0C4]">
                <img src="{{ asset('img/drumstick.svg') }}" alt="Ayamo" class="h-11 w-11 object-contain">
            </div>
            <p class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">Keranjang masih kosong</p>
            <p class="mt-1 text-xs text-[#7A6A58]">Yuk pilih potongan ayam crispy favoritmu dulu!</p>
            <a href="{{ route('mobile.menu') }}"
                class="mt-4 inline-block rounded-2xl bg-[#B83809] px-6 py-3 text-xs font-extrabold text-white shadow-xs transition hover:bg-[#992D06]">
                Lihat Menu
            </a>
        </div>

        {{-- Filled Cart Wrapper --}}
        <div id="cart-filled-wrapper">
            {{-- Card 1: Cart Items Stepper (Rendered instantly on client, 0ms latency) --}}
            <section id="cart-items-list" class="mb-4 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4 space-y-4">
                {{-- Populated by renderMobileCartPage() --}}
            </section>
            <x-input-error :messages="$errors->get('cart')" />
            <x-input-error :messages="$errors->get('items')" />

            {{-- Checkout Form (Only sends order when user clicks Buat Pesanan) --}}
            <form method="post" action="{{ route('mobile.checkout') }}" id="mobile-checkout-form" novalidate>
                @csrf
                <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
                <div id="checkout-hidden-items"></div>

                {{-- Card 2: Data Pesanan --}}
                <section class="mb-4 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4 space-y-3.5">
                    <h2 class="font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Data Pesanan</h2>

                    <div>
                        <label for="customer_name" class="mb-1.5 block text-xs font-semibold text-[#1E1915]">Nama</label>
                        <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}"
                            autocomplete="name"
                            placeholder="Nama Kamu" required minlength="2" maxlength="120"
                            class="w-full rounded-2xl border border-[#D8CBB8] bg-[#FFFDF9] px-4 py-3 text-xs font-medium text-[#1E1915] placeholder-[#8A7D6F] transition focus:border-[#B83809] focus:bg-white focus:outline-none">
                        <p id="err-customer_name" class="mt-1 hidden text-xs font-medium text-red-600"></p>
                        <x-input-error :messages="$errors->get('customer_name')" />
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-semibold text-[#1E1915]">Nomor WhatsApp</label>
                        <input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx" required
                            class="w-full rounded-2xl border border-[#D8CBB8] bg-[#FFFDF9] px-4 py-3 text-xs font-medium text-[#1E1915] placeholder-[#8A7D6F] transition focus:border-[#B83809] focus:bg-white focus:outline-none">
                        <p id="err-phone" class="mt-1 hidden text-xs font-medium text-red-600"></p>
                        <x-input-error :messages="$errors->get('phone')" />
                    </div>

                    <div>
                        <label for="note" class="mb-1.5 block text-xs font-semibold text-[#1E1915]">Catatan ( Opsional )</label>
                        <input id="note" name="note" type="text" value="{{ old('note') }}"
                            placeholder="Misal : Jam sekian di ambil" maxlength="500"
                            class="w-full rounded-2xl border border-[#EFE4CE] bg-[#FFFDF9] px-4 py-3 text-xs font-medium text-[#1E1915] placeholder-[#8A7D6F] transition focus:border-[#B83809] focus:bg-white focus:outline-none">
                        <x-input-error :messages="$errors->get('note')" />
                    </div>
                </section>

                {{-- Card 3: Metode Pembayaran --}}
                <section class="mb-4 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4 space-y-2.5">
                    <h2 class="font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Metode Pembayaran</h2>

                    {{-- Option Tunai --}}
                    <label id="label-method-cod"
                        onclick="selectPaymentMethod('cod')"
                        class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-[1.4px] border-[#3D312A] p-3.5 transition {{ in_array($selectedMethod, ['cod', 'tunai']) ? 'bg-[#F3B825]' : 'bg-[#FFFDF9]' }}">
                        <input type="radio" name="payment_method" value="cod" class="sr-only"
                            {{ in_array($selectedMethod, ['cod', 'tunai']) ? 'checked' : '' }}>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center text-[#1E1915]">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="6" width="20" height="12" rx="2" />
                                <circle cx="12" cy="12" r="2.5" fill="currentColor" />
                                <path d="M6 12h.01M18 12h.01" stroke-width="2.5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <span class="block font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Tunai</span>
                            <span class="block text-[11px] font-medium text-[#2C241D]">Bayar di kasir saat ambil pesanan</span>
                        </div>
                    </label>

                    {{-- Option Transfer --}}
                    <label id="label-method-whatsapp"
                        onclick="selectPaymentMethod('whatsapp')"
                        class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-[1.4px] border-[#3D312A] p-3.5 transition {{ in_array($selectedMethod, ['whatsapp', 'transfer']) ? 'bg-[#F3B825]' : 'bg-[#FFFDF9]' }}">
                        <input type="radio" name="payment_method" value="whatsapp" class="sr-only"
                            {{ in_array($selectedMethod, ['whatsapp', 'transfer']) ? 'checked' : '' }}>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center text-[#1E1915]">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 10l9-6 9 6" />
                                <path d="M4 10h16" />
                                <path d="M6 10v7M10 10v7M14 10v7M18 10v7" />
                                <path d="M3 17h18M2 20h20" />
                            </svg>
                        </div>
                        <div>
                            <span class="block font-['Outfit'] text-sm font-extrabold text-[#1E1915]">Transfer</span>
                            <span class="block text-[11px] font-medium text-[#2C241D]">Bank atau Qris, Konfirmasi lewat WhatsApp</span>
                        </div>
                    </label>
                    <x-input-error :messages="$errors->get('payment_method')" />
                </section>

                {{-- Card 4: Subtotal & Total --}}
                <section class="mb-5 rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] px-4 py-3.5">
                    <div class="flex items-center justify-between text-xs font-semibold text-[#1E1915]">
                        <span>Subtotal</span>
                        <span id="js-cart-subtotal">Rp 0</span>
                    </div>
                    <div class="my-2.5 border-b border-dotted border-[#3D312A]"></div>
                    <div class="flex items-center justify-between font-['Outfit'] text-base font-extrabold text-[#1E1915]">
                        <span>Total</span>
                        <span id="js-cart-grandtotal">Rp 0</span>
                    </div>
                </section>

                {{-- Submit Button --}}
                <button type="submit" id="btn-submit-checkout"
                    class="w-full cursor-pointer rounded-2xl bg-[#B83809] py-3.5 font-['Outfit'] text-sm font-bold text-white shadow-md transition hover:bg-[#992D06] active:scale-[0.99] disabled:opacity-60">
                    Buat Pesanan
                </button>
            </form>
        </div>
    </main>

    <script>
        const SERVER_CATALOG = @json($catalogJson);
        const SESSION_CART = @json($cart ?? []);

        // Merge any server session cart into AyamoCart on initial load
        (function() {
            if (window.AyamoCart && SESSION_CART && typeof SESSION_CART === 'object') {
                const map = window.AyamoCart.getMap();
                let changed = false;
                Object.entries(SESSION_CART).forEach(([pid, qty]) => {
                    const q = parseInt(qty, 10);
                    if (q > 0 && !map[pid] && SERVER_CATALOG[pid]) {
                        map[pid] = {
                            id: pid,
                            name: SERVER_CATALOG[pid].name,
                            price: SERVER_CATALOG[pid].price,
                            stock: SERVER_CATALOG[pid].stock,
                            qty: q
                        };
                        changed = true;
                    }
                });
                if (changed) {
                    window.AyamoCart.saveMap(map);
                }
            }
        })();

        window.renderMobileCartPage = function() {
            const listEl = document.getElementById('cart-items-list');
            const emptyEl = document.getElementById('cart-empty-state');
            const filledEl = document.getElementById('cart-filled-wrapper');
            const hiddenContainer = document.getElementById('checkout-hidden-items');
            const subEl = document.getElementById('js-cart-subtotal');
            const totEl = document.getElementById('js-cart-grandtotal');

            if (!listEl || !window.AyamoCart) return;

            const map = window.AyamoCart.getMap();
            const items = [];
            let total = 0;

            Object.entries(map).forEach(([pid, entry]) => {
                const catalogItem = SERVER_CATALOG[pid];
                const qty = Math.max(0, parseInt(entry.qty || 0, 10));
                if (qty <= 0) return;

                const name = catalogItem ? catalogItem.name : (entry.name || pid);
                const price = catalogItem ? catalogItem.price : parseInt(entry.price || 0, 10);
                const stock = catalogItem ? catalogItem.stock : parseInt(entry.stock || 50, 10);

                items.push({ id: pid, name, price, stock, qty: Math.min(qty, stock) });
                total += price * Math.min(qty, stock);
            });

            if (items.length === 0) {
                emptyEl?.classList.remove('hidden');
                filledEl?.classList.add('hidden');
                return;
            }

            emptyEl?.classList.add('hidden');
            filledEl?.classList.remove('hidden');

            listEl.replaceChildren();

            items.forEach(item => {
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-3';

                const details = document.createElement('div');
                const name = document.createElement('h3');
                name.className = "font-['Outfit'] text-sm font-extrabold text-[#1E1915]";
                name.textContent = item.name;
                const price = document.createElement('p');
                price.className = 'mt-0.5 text-xs font-medium text-[#1E1915]';
                price.textContent = `Rp ${formatRupiah(item.price)}`;
                details.append(name, price);

                const controls = document.createElement('div');
                controls.className = 'flex items-center gap-3';
                const decrement = document.createElement('button');
                decrement.type = 'button';
                decrement.ariaLabel = `Kurangi ${item.name}`;
                decrement.className = 'flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-[#D8CBB8] bg-white text-xl font-bold text-[#1E1915] transition hover:bg-[#F5EFE6] active:scale-90';
                decrement.textContent = '−';
                decrement.addEventListener('click', () => window.AyamoCart.step(item.id, -1));

                const quantity = document.createElement('span');
                quantity.className = 'w-5 text-center text-sm font-extrabold text-[#1E1915]';
                quantity.textContent = String(item.qty);

                const increment = document.createElement('button');
                increment.type = 'button';
                increment.ariaLabel = `Tambah ${item.name}`;
                increment.className = 'flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-[#B83809] text-xl font-bold text-white transition hover:bg-[#992D06] active:scale-90';
                increment.textContent = '+';
                increment.addEventListener('click', () => window.AyamoCart.step(item.id, 1));

                controls.append(decrement, quantity, increment);
                row.append(details, controls);
                listEl.appendChild(row);
            });

            if (hiddenContainer) {
                hiddenContainer.replaceChildren();
                items.forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${item.id}]`;
                    input.value = String(item.qty);
                    hiddenContainer.appendChild(input);
                });
            }

            if (subEl) subEl.textContent = 'Rp ' + formatRupiah(total);
            if (totEl) totEl.textContent = 'Rp ' + formatRupiah(total);
        };

        function selectPaymentMethod(method) {
            const cod = document.getElementById('label-method-cod');
            const wa = document.getElementById('label-method-whatsapp');
            if (!cod || !wa) return;

            if (method === 'cod') {
                cod.classList.add('bg-[#F3B825]');
                cod.classList.remove('bg-[#FFFDF9]');
                wa.classList.remove('bg-[#F3B825]');
                wa.classList.add('bg-[#FFFDF9]');
                cod.querySelector('input').checked = true;
            } else {
                wa.classList.add('bg-[#F3B825]');
                wa.classList.remove('bg-[#FFFDF9]');
                cod.classList.remove('bg-[#F3B825]');
                cod.classList.add('bg-[#FFFDF9]');
                wa.querySelector('input').checked = true;
            }
        }

        // Pre-fill & Validate Checkout Form before sending to server
        document.addEventListener('DOMContentLoaded', () => {
            window.renderMobileCartPage();

            const nameInput = document.getElementById('customer_name');
            const phoneInput = document.getElementById('phone');
            const errName = document.getElementById('err-customer_name');
            const errPhone = document.getElementById('err-phone');
            const form = document.getElementById('mobile-checkout-form');
            const submitBtn = document.getElementById('btn-submit-checkout');

            if (nameInput && !nameInput.value && localStorage.getItem('ayamo_customer_name')) {
                nameInput.value = localStorage.getItem('ayamo_customer_name');
            }
            if (phoneInput && !phoneInput.value && localStorage.getItem('ayamo_customer_phone')) {
                phoneInput.value = localStorage.getItem('ayamo_customer_phone');
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    window.renderMobileCartPage();

                    const summary = window.AyamoCart.getSummary();
                    if (summary.count <= 0) {
                        e.preventDefault();
                        showMobileToast('Keranjang masih kosong!', true);
                        return;
                    }

                    const nameVal = (nameInput?.value || '').trim();
                    const phoneVal = (phoneInput?.value || '').replace(/[\s\-]/g, '');
                    let hasError = false;

                    if (nameVal.length < 2) {
                        hasError = true;
                        if (errName) {
                            errName.textContent = 'Nama wajib diisi (minimal 2 karakter).';
                            errName.classList.remove('hidden');
                        }
                        nameInput?.focus();
                    } else if (errName) {
                        errName.classList.add('hidden');
                    }

                    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,12}$/;
                    if (!phoneRegex.test(phoneVal)) {
                        hasError = true;
                        if (errPhone) {
                            errPhone.textContent = 'Masukkan nomor WhatsApp aktif yang valid (contoh: 081234567890).';
                            errPhone.classList.remove('hidden');
                        }
                        if (nameVal.length >= 2) phoneInput?.focus();
                    } else if (errPhone) {
                        errPhone.classList.add('hidden');
                    }

                    if (hasError) {
                        e.preventDefault();
                        showMobileToast('Mohon lengkapi data pesanan dengan benar.', true);
                        return;
                    }

                    localStorage.setItem('ayamo_customer_name', nameVal);
                    localStorage.setItem('ayamo_customer_phone', phoneVal);

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Memproses Pesanan...';
                    }
                });
            }
        });
    </script>
@endsection
