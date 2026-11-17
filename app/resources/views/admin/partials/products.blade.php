@php
    $categories = $products->pluck('category')->filter()->unique()->values();
    $oldFormKey = old('_form_key');
@endphp

<div class="space-y-6">
    {{-- Header Banner --}}
    <section class="relative overflow-hidden rounded-[28px] border border-[#EBE1D0] bg-gradient-to-r from-[#B83809] via-[#C84612] to-[#7C2405] p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-black tracking-wide text-[#FEF08A] uppercase">
                    Inventori &amp; Menu
                </span>
                <h1 class="mt-2.5 font-['Outfit'] text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Katalog produk
                </h1>
                <p class="mt-1 text-sm font-medium text-white/85">
                    Tambah menu, update harga/foto, atur stok cepat, atau arsipkan varian.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 backdrop-blur-xs">
                    <p class="text-[10px] font-bold text-[#FEF08A] uppercase">Menu Aktif</p>
                    <p class="font-['Outfit'] text-xl font-black text-white">{{ $products->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 backdrop-blur-xs">
                    <p class="text-[10px] font-bold text-[#FEF08A] uppercase">Stok Menipis</p>
                    <p class="font-['Outfit'] text-xl font-black text-white">{{ $stats['low_stock'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 backdrop-blur-xs">
                    <p class="text-[10px] font-bold text-[#FEF08A] uppercase">Diarsipkan</p>
                    <p class="font-['Outfit'] text-xl font-black text-white">{{ $archivedProducts->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Asymmetric Split: Create Form + Catalog List --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[380px_minmax(0,1fr)] xl:items-start">
        {{-- Left: Create Product Form --}}
        <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs xl:sticky xl:top-8">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <span class="rounded-full bg-[#FEF3C7] px-2.5 py-0.5 text-[10px] font-black tracking-wide text-[#92400E] uppercase">
                        Menu Baru
                    </span>
                    <h2 class="mt-1.5 font-['Outfit'] text-xl font-black text-[#1E1915]">
                        Tambah produk
                    </h2>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FAF4EA] text-[#B83809]">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </span>
            </div>

            <form action="/admin/products" method="post" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                @include('admin.product-fields', ['product' => null, 'formKey' => 'create'])

                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#F59E0B] px-5 py-3 font-['Outfit'] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#D97706] active:scale-[0.99]"
                >
                    <span>Simpan Produk Baru</span>
                    <span>→</span>
                </button>
            </form>
        </section>

        {{-- Right: Product Catalog + Archived --}}
        <div class="space-y-6">
            {{-- Toolbar --}}
            <section class="rounded-[24px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs sm:p-5">
                <div class="flex flex-col gap-3.5 md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-wrap gap-2" id="product-cat-pills">
                        <button
                            type="button"
                            data-product-cat="all"
                            class="rounded-full border border-[#B83809] bg-[#B83809] px-3.5 py-1.5 text-xs font-extrabold text-white transition"
                        >
                            Semua ({{ $products->count() }})
                        </button>
                        @foreach ($categories as $category)
                            <button
                                type="button"
                                data-product-cat="{{ mb_strtolower($category) }}"
                                class="rounded-full border border-[#EBE1D0] bg-[#FAF4EA] px-3.5 py-1.5 text-xs font-extrabold text-[#4A3B31] transition hover:border-[#B83809]/40"
                            >
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>

                    <div class="relative w-full md:w-64">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#9A8B7A]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <input
                            id="product-search-input"
                            type="search"
                            placeholder="Cari nama menu..."
                            class="w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 py-2 pr-4 pl-10 text-xs font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
                        >
                    </div>
                </div>
            </section>

            {{-- Active Product Cards --}}
            @if ($products->isEmpty())
                <section class="rounded-[28px] border border-dashed border-[#DFD2BD] bg-[#FFFDF9] p-10 text-center">
                    <p class="font-['Outfit'] text-base font-black text-[#1E1915]">Belum ada menu aktif</p>
                    <p class="mt-1 text-xs font-medium text-[#7C6D5D]">Tambahkan produk baru di formulir sebelah kiri atau pulihkan menu yang diarsipkan.</p>
                </section>
            @else
                <div class="space-y-4" id="active-products-list">
                    @foreach ($products as $product)
                        @php
                            $editKey = 'edit-' . $product->id;
                            $isEditing = $oldFormKey === $editKey;
                            $stockTone = $product->stock <= 0
                                ? 'bg-[#FEE2E2] text-[#991B1B]'
                                : ($product->stock <= 5 ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-[#DCFCE7] text-[#166534]');
                            $stockText = $product->stock <= 0 ? 'Stok Habis (0)' : 'Stok: ' . $product->stock;
                            $imgSrc = str_starts_with((string) $product->image_url, 'http') || str_starts_with((string) $product->image_url, '/')
                                ? $product->image_url
                                : '/' . ltrim((string) $product->image_url, '/');
                        @endphp

                        <article
                            data-product-card
                            data-category="{{ mb_strtolower((string) $product->category) }}"
                            data-search="{{ mb_strtolower($product->name . ' ' . $product->category . ' ' . $product->description) }}"
                            class="overflow-hidden rounded-[26px] border border-[#EBE1D0] bg-[#FFFDF9] shadow-2xs transition hover:border-[#EA580C]/40"
                        >
                            {{-- Card Header Row --}}
                            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-4">
                                    <img
                                        src="{{ $imgSrc }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-16 w-16 shrink-0 rounded-2xl border border-[#EBE1D0] bg-gradient-to-b from-[#FAF4EA] to-[#F3E7D3] object-contain p-1.5"
                                    >
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-['Outfit'] text-lg font-black text-[#1E1915]">{{ $product->name }}</h3>
                                            <span class="rounded-full bg-[#FAF4EA] px-2.5 py-0.5 text-[10px] font-black text-[#7C6D5D]">
                                                {{ $product->category }}
                                            </span>
                                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black {{ $stockTone }}">
                                                {{ $stockText }}
                                            </span>
                                        </div>
                                        <p class="mt-1 line-clamp-2 text-xs font-medium text-[#7C6D5D]">{{ $product->description }}</p>
                                        <p class="mt-1 font-['Outfit'] text-base font-black text-[#B83809]">
                                            Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Quick Stock & Edit/Archive Controls --}}
                                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                    <div class="flex items-center gap-1 rounded-xl border border-[#EBE1D0] bg-[#FAF4EA] p-1">
                                        <form action="/admin/products/{{ $product->id }}/stock" method="post">
                                            @csrf
                                            <input type="hidden" name="delta" value="-1">
                                            <button
                                                type="submit"
                                                @disabled($product->stock <= 0)
                                                title="Kurangi 1 stok"
                                                class="rounded-lg px-2.5 py-1 text-xs font-black text-[#4A3B31] transition hover:bg-white disabled:opacity-40"
                                            >
                                                -1
                                            </button>
                                        </form>
                                        <span class="px-1.5 text-xs font-black text-[#1E1915]">{{ $product->stock }}</span>
                                        <form action="/admin/products/{{ $product->id }}/stock" method="post">
                                            @csrf
                                            <input type="hidden" name="delta" value="1">
                                            <button
                                                type="submit"
                                                title="Tambah 1 stok"
                                                class="rounded-lg px-2.5 py-1 text-xs font-black text-[#166534] transition hover:bg-white"
                                            >
                                                +1
                                            </button>
                                        </form>
                                        <form action="/admin/products/{{ $product->id }}/stock" method="post">
                                            @csrf
                                            <input type="hidden" name="delta" value="5">
                                            <button
                                                type="submit"
                                                title="Restok cepat +5"
                                                class="rounded-lg bg-[#DCFCE7] px-2.5 py-1 text-xs font-black text-[#166534] transition hover:bg-[#BBF7D0]"
                                            >
                                                +5
                                            </button>
                                        </form>
                                    </div>

                                    <button
                                        type="button"
                                        data-toggle-panel="{{ $editKey }}"
                                        aria-expanded="{{ $isEditing ? 'true' : 'false' }}"
                                        class="rounded-xl border border-[#DFD2BD] bg-white px-3.5 py-2 text-xs font-black text-[#1E1915] transition hover:border-[#B83809] hover:text-[#B83809]"
                                    >
                                        {{ $isEditing ? 'Tutup Edit' : 'Edit Menu' }}
                                    </button>

                                    <form
                                        action="/admin/products/{{ $product->id }}/delete"
                                        method="post"
                                        data-confirm="Menu {{ $product->name }} akan disembunyikan dari etalase pelanggan dan bisa dipulihkan kapan saja."
                                        data-confirm-title="Arsipkan {{ $product->name }}?"
                                        data-confirm-ok="Ya, Arsipkan Menu"
                                        data-confirm-cancel="Batal"
                                        data-confirm-variant="danger"
                                    >
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-3 py-2 text-xs font-black text-[#991B1B] transition hover:bg-[#FEE2E2]">
                                            Arsipkan
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Collapsible Edit Form Drawer --}}
                            <div id="{{ $editKey }}" class="{{ $isEditing ? '' : 'hidden' }} border-t border-[#EBE1D0] bg-[#FAF4EA]/45 p-5">
                                <form action="/admin/products/{{ $product->id }}" method="post" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    @include('admin.product-fields', ['product' => $product, 'formKey' => $editKey])

                                    <div class="flex flex-wrap justify-end gap-2.5 pt-2">
                                        <button
                                            type="button"
                                            data-toggle-panel="{{ $editKey }}"
                                            class="rounded-xl border border-[#DFD2BD] bg-white px-4 py-2 text-xs font-black text-[#4A3B31] transition hover:bg-[#FAF4EA]"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            class="rounded-xl bg-[#B83809] px-5 py-2 text-xs font-black text-white shadow-2xs transition hover:bg-[#992D06]"
                                        >
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <p id="products-empty-filter" class="hidden rounded-[24px] border border-dashed border-[#DFD2BD] bg-[#FFFDF9] p-8 text-center text-sm font-bold text-[#7C6D5D]">
                    Tidak ada menu yang cocok dengan filter tersebut.
                </p>
            @endif

            {{-- Archived Products Section --}}
            @if ($archivedProducts->isNotEmpty())
                <section class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h2 class="font-['Outfit'] text-lg font-black text-[#1E1915]">Produk Diarsipkan</h2>
                            <p class="text-xs font-semibold text-[#7C6D5D]">
                                Menu ini disembunyikan dari pelanggan tetapi aman di riwayat transaksi.
                            </p>
                        </div>
                        <span class="rounded-full bg-[#F3E7D3] px-3 py-1 text-xs font-black text-[#4A3B31]">
                            {{ $archivedProducts->count() }} arsip
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach ($archivedProducts as $archived)
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/60 p-3.5">
                                <div class="flex min-w-0 items-center gap-3">
                                    <img
                                        src="{{ asset(ltrim((string) $archived->image_url, '/')) }}"
                                        alt="{{ $archived->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-11 w-11 shrink-0 rounded-xl border border-[#EBE1D0] bg-white object-contain p-1 opacity-75"
                                    >
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-[#1E1915]">{{ $archived->name }}</p>
                                        <p class="text-[11px] font-semibold text-[#7C6D5D]">
                                            {{ $archived->category }} · Rp{{ number_format($archived->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <form action="/admin/products/{{ $archived->id }}/restore" method="post" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-[#166534] px-3 py-1.5 text-xs font-black text-white transition hover:bg-[#15803D]">
                                        Pulihkan
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    // Collapsible edit panels
    document.querySelectorAll('[data-toggle-panel]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.togglePanel;
            const panel = targetId ? document.getElementById(targetId) : null;
            if (!panel) return;
            const willOpen = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !willOpen);

            const headerToggle = document.querySelector(`button[data-toggle-panel="${targetId}"][aria-expanded]`);
            if (headerToggle) {
                headerToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                headerToggle.textContent = willOpen ? 'Tutup Edit' : 'Edit Menu';
            }
        });
    });

    // Quick category buttons inside product-fields
    document.querySelectorAll('[data-set-target]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.setTarget);
            if (input) input.value = btn.dataset.setValue || '';
        });
    });

    // Preset image picker buttons + URL live preview
    document.querySelectorAll('[data-pick-image]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const urlInput = document.getElementById(btn.dataset.urlInput);
            const previewImg = document.getElementById(btn.dataset.previewImg);
            const path = btn.dataset.pickImage || '';
            if (urlInput) urlInput.value = path;
            if (previewImg && path) previewImg.src = path;
        });
    });

    document.querySelectorAll('[data-preview-target]').forEach((input) => {
        input.addEventListener('input', () => {
            const previewImg = document.getElementById(input.dataset.previewTarget);
            const raw = (input.value || '').trim();
            if (previewImg && raw) {
                previewImg.src = raw.startsWith('http') || raw.startsWith('/') ? raw : '/' + raw;
            }
        });
    });

    document.querySelectorAll('[data-file-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            const previewImg = document.getElementById(input.dataset.filePreview);
            if (!file || !previewImg) return;
            const objectUrl = URL.createObjectURL(file);
            previewImg.src = objectUrl;
            previewImg.onload = () => URL.revokeObjectURL(objectUrl);
        });
    });

    // Category filter pills & search
    const cards = Array.from(document.querySelectorAll('[data-product-card]'));
    const pills = Array.from(document.querySelectorAll('[data-product-cat]'));
    const searchInput = document.getElementById('product-search-input');
    const emptyEl = document.getElementById('products-empty-filter');
    if (!cards.length) return;

    let activeCat = 'all';

    const applyFilter = () => {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const matchesCat = activeCat === 'all' || card.dataset.category === activeCat;
            const matchesQuery = !query || (card.dataset.search || '').includes(query);
            const show = matchesCat && matchesQuery;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    };

    pills.forEach((pill) => {
        pill.addEventListener('click', () => {
            activeCat = pill.dataset.productCat || 'all';
            pills.forEach((p) => {
                const selected = p === pill;
                p.className = selected
                    ? 'rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition border-[#B83809] bg-[#B83809] text-white'
                    : 'rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition border-[#EBE1D0] bg-[#FAF4EA] text-[#4A3B31] hover:border-[#B83809]/40';
            });
            applyFilter();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilter);
})();
</script>
