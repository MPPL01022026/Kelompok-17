<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#B83809">
    <link rel="icon" type="image/webp" href="{{ asset('img/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.webp') }}">
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
    {{-- Top Navigation Bar --}}
    <header class="sticky top-0 z-50 border-b border-[#EADFC9]/80 bg-[#FFFDF9]/92 shadow-[0_4px_24px_rgba(34,24,14,0.05)] backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            {{-- Brand Logo & Tagline --}}
            <a href="{{ route('web.home') }}" class="group flex shrink-0 items-center gap-3 focus:outline-none" aria-label="Beranda Ayamo">
                <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-[16px] border border-[#F3E3D0] bg-[#FFF8F0] p-1.5 shadow-2xs transition duration-200 group-hover:scale-105">
                    <img src="{{ asset('img/logo.webp') }}" alt="Ayamo Logo" class="h-full w-full object-contain">
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
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.home') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Beranda
                </a>
                <a href="{{ route('web.menu') }}"
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.menu') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Menu Katalog
                </a>
                <a href="{{ route('web.history') }}"
                    class="rounded-full px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('web.history', 'web.orders.*') ? 'bg-[#FEF3C7] text-[#9A3412] shadow-2xs' : 'text-[#6B5E50] hover:bg-[#FAF4EA] hover:text-[#EA580C]' }}">
                    Riwayat
                </a>
            </nav>

            {{-- Search Bar --}}
            <form action="{{ route('web.menu') }}" method="get" class="hidden max-w-xs flex-1 lg:block xl:max-w-sm">
                <label class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#8C7E70]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                    </svg>
                    <input name="q" value="{{ request('q') }}" placeholder="Cari dada, paha, sayap..."
                        class="h-10 w-full rounded-full border border-[#E5D8C4] bg-[#F9F5EC] pl-10 pr-4 text-xs font-semibold text-[#1E1915] placeholder-[#8A7D6F] transition outline-none focus:border-[#EA580C] focus:bg-white focus:ring-2 focus:ring-[#EA580C]/20">
                </label>
            </form>

            {{-- Right Actions: Store Status & Cart Button --}}
            <div class="flex items-center gap-3">
                {{-- Store Status --}}
                <div class="hidden items-center gap-1.5 rounded-full border border-[#DCFCE7] bg-[#F0FDF4] px-3 py-1.5 text-xs font-extrabold text-[#166534] shadow-2xs sm:flex">
                    <span class="h-2 w-2 rounded-full bg-[#22C55E]"></span>
                    <span>Buka 15.00 - 03.00</span>
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

    {{-- Flash Notifications --}}
    @if (session('status') || session('error') || $errors->any())
        <div class="fixed top-24 right-6 z-50 max-w-sm rounded-2xl border p-4 text-xs font-bold shadow-xl backdrop-blur-md transition {{ session('error') || $errors->any() ? 'border-[#FCA5A5] bg-[#FEF2F2]/95 text-[#991B1B]' : 'border-[#86EFAC] bg-[#F0FDF4]/95 text-[#166534]' }}" role="status">
            {{ session('error') ?? $errors->first() ?? session('status') }}
        </div>
    @endif

    {{-- Main Page Content --}}
    @yield('content')

    {{-- Modern Culinary Footer --}}
    <footer class="mt-24 border-t border-[#3B2E24] bg-[#221A14] text-[#EFE7DC]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
                {{-- Col 1: Brand & Bio --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-[14px] bg-[#FFF8F0] p-1.5 shadow-2xs">
                            <img src="{{ asset('img/logo.webp') }}" alt="Ayamo Logo" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <span class="font-['Outfit'] text-2xl font-black leading-none text-[#F97316]">AYAMO</span>
                            <p class="text-[10px] font-bold tracking-wider text-[#A89886]">EAT. CHILL. REPEAT.</p>
                        </div>
                    </div>
                    <p class="text-xs leading-relaxed text-[#B5A695]">
                        Ayam goreng crispy dengan bumbu rahasia khas Ayamo. Renyah maksimal di luar, lembut juicy di dalam, dan selalu digoreng hangat saat kamu pesan.
                    </p>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] font-bold text-[#F3B825]">
                        <span>🟢 Buka Setiap Hari (15.00 - 03.00)</span>
                    </div>
                </div>

                {{-- Col 2: Quick Links --}}
                <div>
                    <h3 class="font-['Outfit'] text-sm font-extrabold tracking-wider text-white uppercase">Navigasi</h3>
                    <ul class="mt-4 space-y-2.5 text-xs font-semibold text-[#B5A695]">
                        <li><a href="{{ route('web.home') }}" class="transition hover:text-[#F3B825]">Beranda Utama</a></li>
                        <li><a href="{{ route('web.menu') }}" class="transition hover:text-[#F3B825]">Katalog Menu Lengkap</a></li>
                        <li><a href="{{ route('web.cart') }}" class="transition hover:text-[#F3B825]">Keranjang Belanja</a></li>
                        <li><a href="{{ route('web.history') }}" class="transition hover:text-[#F3B825]">Riwayat & Lacak Pesanan</a></li>
                        <li><a href="/mobile" class="transition hover:text-[#F3B825]">Tampilan Mobile App</a></li>
                    </ul>
                </div>

                {{-- Col 3: Menu Favorit --}}
                <div>
                    <h3 class="font-['Outfit'] text-sm font-extrabold tracking-wider text-white uppercase">Menu Favorit</h3>
                    <ul class="mt-4 space-y-2.5 text-xs font-semibold text-[#B5A695]">
                        <li><a href="{{ route('web.menu', ['q' => 'Dada']) }}" class="transition hover:text-[#F3B825]">Dada Ayam Crispy — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Paha']) }}" class="transition hover:text-[#F3B825]">Paha Atas & Bawah — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Sayap']) }}" class="transition hover:text-[#F3B825]">Sayap Ayam Crispy — Rp7.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Bakso']) }}" class="transition hover:text-[#F3B825]">Bakso Goreng Stick — Rp1.000</a></li>
                        <li><a href="{{ route('web.menu', ['q' => 'Ceker']) }}" class="transition hover:text-[#F3B825]">Ceker Ayam Gurih — Rp500</a></li>
                    </ul>
                </div>

                {{-- Col 4: Lokasi & Bantuan --}}
                <div>
                    <h3 class="font-['Outfit'] text-sm font-extrabold tracking-wider text-white uppercase">Outlet & Bantuan</h3>
                    <div class="mt-4 space-y-3 text-xs leading-relaxed text-[#B5A695]">
                        <p class="flex items-start gap-2">
                            <span class="shrink-0 text-base">📍</span>
                            <span>Jl. Prof. Dr. Sharif Thayib - Gp Teungoh, Kec. Langsa Kota, Kota Langsa, Aceh</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="shrink-0 text-base">🕒</span>
                            <span>Buka jam 15.00 s/d 03.00 WIB</span>
                        </p>
                        <div class="pt-2">
                            <a href="https://wa.me/62895346200506?text=Halo%20Ayamo,%20saya%20ingin%20bertanya%20seputar%20menu"
                                target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 rounded-full bg-[#16A34A] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#15803D] active:scale-95">
                                <span>💬 Chat WhatsApp Outlet</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-[#8F7F6E] sm:flex-row">
                <p>© {{ date('Y') }} Ayamo Fried Chicken. Semua hak dilindungi.</p>
                <p class="flex items-center gap-1.5 font-medium">
                    <span>Sajian renyah & hangat, dibuat saat dipesan.</span>
                </p>
            </div>
        </div>
    </footer>

    <script>
        window.AyamoWebCartConfig = {
            syncUrl: @json(route('web.cart.update')),
            csrfToken: @json(csrf_token()),
        };

        @if (session('clear_mobile_cart'))
            document.addEventListener('ayamo-cart-ready', () => window.AyamoCart.clear(), { once: true });
        @endif
    </script>
</body>
</html>
