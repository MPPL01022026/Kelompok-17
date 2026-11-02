<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#B83809">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Ayamo">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/webp" href="{{ asset('img/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.webp') }}">
    <title>@yield('title', 'AYAMO | Eat, Chill, Repeat')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .mobile-shell {
            overflow-x: clip;
        }
        .mobile-shell main {
            min-width: 0;
            width: 100%;
        }
        @keyframes popIn {
            0% { transform: scale(0.94); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop {
            animation: popIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body class="min-h-screen w-full bg-[#E9DEC9] font-['Plus_Jakarta_Sans'] text-[#1E1915] antialiased selection:bg-[#F3B825] selection:text-[#1E1915]">
    <div class="mobile-shell relative mx-auto flex min-h-screen w-full max-w-[430px] flex-col overflow-x-hidden bg-[#FFFDF8] shadow-[0_20px_60px_rgba(34,24,14,0.18)]">

        {{-- Floating Toast Notification --}}
        <div id="mobile-toast-container" class="pointer-events-none fixed top-4 left-1/2 z-50 flex w-full max-w-[380px] -translate-x-1/2 flex-col items-center px-4">
            @if (session('status'))
                <div id="mobile-flash"
                    onclick="this.remove()"
                    class="pointer-events-auto flex items-center gap-2 rounded-full bg-[#1E1915]/95 px-4 py-2.5 text-xs font-bold text-white shadow-lg backdrop-blur-xs transition">
                    <span class="flex h-4 w-4 items-center justify-center rounded-full bg-[#22C55E] text-[10px] text-white">✓</span>
                    <span>{{ session('status') }}</span>
                </div>
                <script>
                    setTimeout(() => {
                        const el = document.getElementById('mobile-flash');
                        if (el) el.remove();
                    }, 2600);
                </script>
            @endif
        </div>

        <x-modal-notification />

        {{-- Main View Wrapper --}}
        <div class="flex min-w-0 flex-1 flex-col w-full">
            @yield('content')
        </div>
    </div>

    <script>
        function formatRupiah(num) {
            return Number(num || 0).toLocaleString('id-ID');
        }

        function showMobileToast(message, isError = false) {
            const container = document.getElementById('mobile-toast-container');
            if (!container) return;
            const existing = container.querySelector('.js-dynamic-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'js-dynamic-toast animate-pop pointer-events-auto flex items-center gap-2 rounded-full bg-[#1E1915]/95 px-4 py-2.5 text-xs font-bold text-white shadow-lg backdrop-blur-xs';
            const badgeColor = isError ? 'bg-[#DC2626]' : 'bg-[#22C55E]';
            const badgeIcon = isError ? '!' : '✓';
            const badge = document.createElement('span');
            badge.className = `flex h-4 w-4 shrink-0 items-center justify-center rounded-full ${badgeColor} text-[10px] text-white`;
            badge.textContent = badgeIcon;
            const text = document.createElement('span');
            text.textContent = String(message);
            toast.append(badge, text);
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.22s ease';
                setTimeout(() => toast.remove(), 240);
            }, 1800);
        }

        // Instant Client-Side Cart Engine (0ms latency, only sends on Checkout)
        window.AyamoCart = {
            STORAGE_KEY: 'ayamo_cart_v1',

            getMap() {
                try {
                    const raw = localStorage.getItem(this.STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : {};
                    return (parsed && typeof parsed === 'object') ? parsed : {};
                } catch (e) {
                    return {};
                }
            },

            saveMap(map) {
                try {
                    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(map));
                } catch (e) {}
                this.syncUI();
            },

            clear() {
                try {
                    localStorage.removeItem(this.STORAGE_KEY);
                } catch (e) {}
                this.syncUI();
            },

            add(product, delta = 1) {
                if (!product || !product.id) return;
                const map = this.getMap();
                const maxStock = Math.max(0, parseInt(product.stock ?? 50, 10));
                if (maxStock < 1) {
                    showMobileToast('Stok produk sedang habis.', true);
                    return;
                }

                const existing = map[product.id] || {
                    id: String(product.id),
                    name: String(product.name || product.id),
                    price: Math.max(0, parseInt(product.price || 0, 10)),
                    stock: maxStock,
                    qty: 0
                };

                const currentQty = Math.max(0, parseInt(existing.qty || 0, 10));
                const nextQty = currentQty + delta;
                if (nextQty > maxStock) {
                    showMobileToast(`Maksimal stok tersedia (${maxStock}) tercapai.`, true);
                    return;
                }

                if (nextQty <= 0) {
                    delete map[product.id];
                } else {
                    existing.qty = nextQty;
                    existing.price = Math.max(0, parseInt(product.price || existing.price, 10));
                    existing.name = String(product.name || existing.name);
                    existing.stock = maxStock;
                    map[product.id] = existing;
                }

                this.saveMap(map);
                if (delta > 0) {
                    showMobileToast(`${existing.name} ditambahkan (${existing.qty}x)`);
                }
            },

            step(productId, delta) {
                const map = this.getMap();
                const item = map[productId];
                if (!item) return;
                const maxStock = Math.max(1, parseInt(item.stock ?? 50, 10));
                const currentQty = Math.max(0, parseInt(item.qty || 0, 10));
                const nextQty = currentQty + delta;

                if (nextQty > maxStock) {
                    showMobileToast(`Stok maksimal ${maxStock} item.`, true);
                    return;
                }

                if (nextQty <= 0) {
                    delete map[productId];
                } else {
                    item.qty = nextQty;
                    map[productId] = item;
                }
                this.saveMap(map);
            },

            getSummary() {
                const map = this.getMap();
                let count = 0;
                let total = 0;
                Object.values(map).forEach(item => {
                    const q = Math.max(0, parseInt(item.qty || 0, 10));
                    const p = Math.max(0, parseInt(item.price || 0, 10));
                    if (q > 0) {
                        count += q;
                        total += q * p;
                    }
                });
                return { count, total, items: Object.values(map).filter(i => i.qty > 0) };
            },

            syncUI() {
                const map = this.getMap();
                const { count, total } = this.getSummary();

                // Update Sticky Floating Cart Bars
                document.querySelectorAll('.js-cart-bar-wrapper').forEach(wrapper => {
                    if (count > 0) {
                        wrapper.classList.remove('hidden');
                        wrapper.querySelectorAll('.js-cart-count').forEach(el => el.textContent = count + ' Item');
                        wrapper.querySelectorAll('.js-cart-total').forEach(el => el.textContent = 'Rp. ' + formatRupiah(total));
                    } else {
                        wrapper.classList.add('hidden');
                    }
                });

                // Update "Tambah (x)" buttons on Beranda & Menu
                document.querySelectorAll('[data-product-btn]').forEach(btn => {
                    const pid = btn.getAttribute('data-product-btn');
                    const qty = Math.max(0, parseInt(map[pid]?.qty || 0, 10));
                    btn.textContent = qty > 0 ? `Tambah (${qty})` : 'Tambah';
                });

                // Trigger custom hook if on Cart page
                if (typeof window.renderMobileCartPage === 'function') {
                    window.renderMobileCartPage();
                }
            }
        };

        @if (session('clear_mobile_cart'))
            window.AyamoCart.clear();
        @endif

        @if (session('reorder_payload'))
            (function() {
                const reorderItems = @json(session('reorder_payload'));
                if (Array.isArray(reorderItems)) {
                    const map = window.AyamoCart.getMap();
                    reorderItems.forEach(item => {
                        if (!item || !item.id) return;
                        const maxStock = Math.max(1, parseInt(item.stock ?? 50, 10));
                        const cur = map[item.id]?.qty || 0;
                        map[item.id] = {
                            id: item.id,
                            name: item.name,
                            price: parseInt(item.price, 10),
                            stock: maxStock,
                            qty: Math.min(cur + parseInt(item.qty || 1, 10), maxStock)
                        };
                    });
                    window.AyamoCart.saveMap(map);
                }
            })();
        @endif

        document.addEventListener('DOMContentLoaded', () => {
            window.AyamoCart.syncUI();
        });

        // Instant click handler for all [data-add-product] buttons/forms
        document.addEventListener('submit', function(e) {
            const form = e.target.closest('form[data-ajax-cart]');
            if (!form) return;
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.classList.add('scale-95');
                setTimeout(() => btn.classList.remove('scale-95'), 130);
            }

            const product = {
                id: form.dataset.productId || form.querySelector('input[name="product_id"]')?.value,
                name: form.dataset.productName || 'Menu Ayamo',
                price: parseInt(form.dataset.productPrice || '0', 10),
                stock: parseInt(form.dataset.productStock || '50', 10),
            };

            window.AyamoCart.add(product, 1);
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>

</html>
