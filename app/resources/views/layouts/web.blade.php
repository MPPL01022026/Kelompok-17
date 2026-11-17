<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#B83809">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <title>@yield('title', 'AYAMO — Ayam Crispy Favorit | Eat, Chill, Repeat')</title>

    {{-- Two-way Auto-redirect to /mobile if viewport <= 768px --}}
    <script>
        (function() {
            function checkMobileViewport() {
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                if (!isStandalone && window.innerWidth <= 768) {
                    window.location.replace('/mobile');
                }
            }
            checkMobileViewport();
            window.addEventListener('resize', checkMobileViewport);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#FFFDF8] font-['Plus_Jakarta_Sans'] text-[#1E1915] antialiased selection:bg-[#F3B825] selection:text-[#1E1915]">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-xl focus:bg-[#993500] focus:px-4 focus:py-2.5 focus:text-sm focus:font-bold focus:text-white focus:shadow-lg">
        Lewati ke konten utama
    </a>
    @php
        $storeSettings = $settings ?? \App\Models\Settings::getMap();
        $storeTime = $storeSettings['store_time'] ?? '15.00 - 03.00';
        $storeStatus = \App\Models\Settings::getStoreStatus($storeSettings);
        $storeAddress = $storeSettings['address'] ?? 'Jl. Prof. Dr. Sharif Thayib - Gp Teungoh, Kec. Langsa Kota, Kota Langsa, Aceh';
        $waDigits = preg_replace('/[^0-9]/', '', $storeSettings['phone_number'] ?? '62895346200506');
        if (str_starts_with($waDigits, '0')) {
            $waDigits = '62' . substr($waDigits, 1);
        }
    @endphp

    <x-modal-notification />
    <x-modal-confirmation />

    {{-- Top Navigation Bar --}}
    <header class="sticky top-0 z-50 border-b border-[#EADFC9]/80 bg-[#FFFDF9]/92 shadow-[0_4px_24px_rgba(34,24,14,0.05)] backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            {{-- Brand Logo & Tagline --}}
            <a href="{{ route('web.home') }}" class="group flex shrink-0 items-center gap-3 focus:outline-none" aria-label="Beranda Ayamo">
                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-[14px] shadow-2xs transition duration-200 group-hover:scale-105">
                    <img src="{{ asset('img/logo.webp') }}" alt="Ayamo Logo" class="h-full w-full object-contain p-0.5">
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-2">
                        <span class="font-['Outfit'] text-2xl font-black leading-none tracking-tight text-[#EA580C]">AYAMO</span>
                        <span class="rounded-full bg-[#FEF3C7] px-2 py-0.5 text-[10px] font-extrabold text-[#92400E]">Crispy</span>
                    </div>
                    <p class="mt-1 text-[10px] font-extrabold tracking-wider text-[#7C6D5D]">EAT. CHILL. REPEAT.</p>
                </div>
            </a>

            {{-- Nav Links --}}
            <nav class="hidden items-center gap-1.5 md:flex" aria-label="Navigasi Utama">
                <a href="{{ route('web.home') }}"
                    @if (request()->routeIs('web.home')) aria-current="page" @endif
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.home') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Beranda
                </a>
                <a href="{{ route('web.menu') }}"
                    @if (request()->routeIs('web.menu')) aria-current="page" @endif
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.menu') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Menu Katalog
                </a>
                <a href="{{ route('web.history') }}"
                    @if (request()->routeIs('web.history', 'web.orders.*')) aria-current="page" @endif
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.history', 'web.orders.*') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Riwayat
                </a>
            </nav>

            {{-- Search Bar --}}
            <form action="{{ route('web.menu') }}" method="get" role="search" aria-label="Pencarian Menu" class="hidden max-w-xs flex-1 lg:block xl:max-w-sm">
                <label class="relative flex items-center">
                    <span class="sr-only">Cari menu Ayamo</span>
                    <svg aria-hidden="true" class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#8C7E70]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                    </svg>
                    <input name="q" type="search" value="{{ request('q') }}" placeholder="Cari dada, paha, sayap..." aria-label="Cari menu Ayamo"
                        class="h-10 w-full rounded-full border border-[#E5D8C4] bg-[#F9F5EC] pl-10 pr-4 text-xs font-semibold text-[#1E1915] placeholder-[#8A7D6F] transition outline-none focus:border-[#EA580C] focus:bg-white focus:ring-2 focus:ring-[#EA580C]/20">
                </label>
            </form>

            {{-- Right Actions: Store Status & Cart Button --}}
            <div class="flex items-center gap-3">
                {{-- Store Status --}}
                <div class="hidden items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-extrabold shadow-2xs sm:flex {{ $storeStatus['is_open'] ? 'border-[#DCFCE7] bg-[#F0FDF4] text-[#166534]' : 'border-[#FECACA] bg-[#FEF2F2] text-[#991B1B]' }}">
                    <span class="h-2 w-2 rounded-full {{ $storeStatus['is_open'] ? 'bg-[#22C55E]' : 'bg-[#EF4444]' }}"></span>
                    <span>{{ $storeStatus['badge'] }}</span>
                </div>

                {{-- Cart Button --}}
                <a href="{{ route('web.cart') }}" data-cart-sync-link
                    class="group flex h-11 items-center gap-2.5 rounded-full bg-[#F3B825] px-5 text-sm font-extrabold text-[#1E1915] shadow-xs transition hover:bg-[#FACC15] active:scale-95">
                    <svg class="h-5 w-5 transition group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 8H6" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="10" cy="20" r="1.2" fill="currentColor"/>
                        <circle cx="18" cy="20" r="1.2" fill="currentColor"/>
                    </svg>
                    <span>Keranjang</span>
                    <span data-cart-count
                        class="grid min-w-[22px] place-items-center rounded-full bg-[#B83809] px-1.5 py-0.5 text-xs font-black text-white shadow-2xs">
                        {{ $cartCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>
    </header>

    @if (! $storeStatus['is_open'])
        <div id="web-store-closed-banner" role="alert"
            class="border-b border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-center text-xs font-bold text-[#991B1B] sm:px-6">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-2">
                <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#DC2626]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <span class="font-black uppercase">{{ $storeStatus['label'] }}:</span>
                <span>{{ $storeStatus['reason'] }}</span>
                <span class="text-[#7F1D1D]/80">(Jam operasional normal: {{ $storeTime }} WIB)</span>
            </div>
        </div>
    @endif

    {{-- Main Page Content --}}
    @yield('content')

    {{-- Modern Culinary Footer --}}
    <footer class="mt-24 border-t-4 border-[#B83809] bg-[#1E1610] text-left text-[#EFE7DC]">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-[1.35fr_0.9fr_1fr_1.25fr]">
                {{-- Col 1: Brand & Bio --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#F3B825]/40 bg-[#FFFDF9] p-1 shadow-md">
                            <img src="{{ asset('img/logo.webp') }}" alt="Ayamo Logo" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <span class="font-['Outfit'] text-2xl font-black leading-none text-white">AYAMO</span>
                            <p class="mt-0.5 text-[10px] font-bold tracking-wider text-[#F3B825]">EAT. CHILL. REPEAT.</p>
                        </div>
                    </div>
                    <p class="max-w-xs text-xs leading-relaxed text-[#CFC3B5]">
                        Ayam crispy bumbu khas Ayamo. Digoreng hangat saat dipesan, siap ambil tanpa antre di outlet.
                    </p>
                    <div class="inline-flex items-center gap-2 rounded-full border px-3.5 py-1.5 text-[11px] font-bold {{ $storeStatus['is_open'] ? 'border-[#F3B825]/30 bg-white/5 text-[#F3B825]' : 'border-[#FCA5A5]/40 bg-[#991B1B]/30 text-[#FECACA]' }}">
                        <span class="h-2 w-2 rounded-full {{ $storeStatus['is_open'] ? 'bg-[#22C55E]' : 'bg-[#EF4444]' }}"></span>
                        <span>{{ $storeStatus['is_open'] ? "Buka Setiap Hari ({$storeTime})" : "{$storeStatus['label']} — {$storeStatus['badge']}" }}</span>
                    </div>
                </div>

                {{-- Col 2: Quick Links --}}
                <div>
                    <h3 class="font-['Outfit'] text-xs font-black tracking-wider text-[#F3B825] uppercase">Navigasi</h3>
                    <ul class="mt-4 space-y-2.5 text-xs font-semibold text-[#D6C9BA]">
                        <li><a href="{{ route('web.home') }}" class="transition hover:text-white">Beranda Utama</a></li>
                        <li><a href="{{ route('web.menu') }}" class="transition hover:text-white">Katalog Menu Lengkap</a></li>
                        <li><a href="{{ route('web.cart') }}" data-cart-sync-link class="transition hover:text-white">Keranjang Belanja</a></li>
                        <li><a href="{{ route('web.history') }}" class="transition hover:text-white">Riwayat &amp; Lacak Pesanan</a></li>
                        <li><a href="/mobile" class="transition hover:text-white">Tampilan Mobile App</a></li>
                    </ul>
                </div>

                {{-- Col 3: Menu Favorit --}}
                <div>
                    <h3 class="font-['Outfit'] text-xs font-black tracking-wider text-[#F3B825] uppercase">Menu Favorit</h3>
                    <ul class="mt-4 space-y-2.5 text-xs font-semibold text-[#D6C9BA]">
                        <li><a href="{{ route('web.menu', ['q' => 'Dada']) }}" class="transition hover:text-white">Dada Ayam Crispy — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Paha']) }}" class="transition hover:text-white">Paha Atas &amp; Bawah — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Sayap']) }}" class="transition hover:text-white">Sayap Ayam Crispy — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Bakso']) }}" class="transition hover:text-white">Bakso Goreng — Rp1.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Ceker']) }}" class="transition hover:text-white">Ceker Ayam Gurih — Rp500</a></li>
                    </ul>
                </div>

                {{-- Col 4: Lokasi & Bantuan --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <h3 class="font-['Outfit'] text-xs font-black tracking-wider text-[#F3B825] uppercase">Outlet &amp; Bantuan</h3>
                    <div class="mt-4 space-y-3 text-xs leading-relaxed text-[#D6C9BA]">
                        <p class="flex items-start gap-2.5">
                            <svg aria-hidden="true" class="mt-0.5 h-4 w-4 shrink-0 text-[#F3B825]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>{{ $storeAddress }}</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#F3B825]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Buka {{ $storeTime }} WIB</span>
                        </p>
                        <div class="pt-2">
                            <a href="https://wa.me/{{ $waDigits }}?text=Halo%20Ayamo,%20saya%20ingin%20bertanya%20seputar%20menu"
                                target="_blank" rel="noopener"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#16A34A] px-4 py-2.5 text-xs font-black text-white shadow-xs transition hover:bg-[#15803D] active:scale-95">
                                <span>Chat WhatsApp Outlet</span>
                                <span>↗</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-white/10 pt-7 text-xs text-[#A89886] sm:flex-row sm:items-center">
                <p>© {{ date('Y') }} Ayamo Fried Chicken. Semua hak dilindungi.</p>
                <p class="font-medium text-[#D6C9BA]">Sajian renyah &amp; hangat, dibuat saat dipesan.</p>
            </div>
        </div>
    </footer>

    <script>
        window.AyamoWebCartConfig = {
            syncUrl: @json(route('web.cart.update')),
            csrfToken: @json(csrf_token()),
        };

        @if (session('clear_mobile_cart'))
            try { localStorage.removeItem('ayamo_cart_v1'); } catch (e) {}
            document.addEventListener('ayamo-cart-ready', () => window.AyamoCart?.clear(), { once: true });
        @endif

        @if (session('reorder_payload'))
            (function() {
                const reorderItems = @json(session('reorder_payload'));
                if (!Array.isArray(reorderItems)) return;
                try {
                    const raw = localStorage.getItem('ayamo_cart_v1');
                    const map = raw ? JSON.parse(raw) : {};
                    const nextMap = (map && typeof map === 'object') ? map : {};
                    reorderItems.forEach((item) => {
                        if (!item || !item.id) return;
                        const maxStock = Math.max(1, parseInt(item.stock ?? 50, 10));
                        const cur = parseInt(nextMap[item.id]?.qty || 0, 10);
                        nextMap[item.id] = {
                            id: String(item.id),
                            name: String(item.name || item.id),
                            price: parseInt(item.price || 0, 10),
                            stock: maxStock,
                            qty: Math.min(cur + parseInt(item.qty || 1, 10), maxStock),
                        };
                    });
                    localStorage.setItem('ayamo_cart_v1', JSON.stringify(nextMap));
                } catch (e) {}
                document.addEventListener('ayamo-cart-ready', () => window.AyamoCart?.syncUI(), { once: true });
            })();
        @endif
    </script>
</body>
</html>
