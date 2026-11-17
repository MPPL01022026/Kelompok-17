@php
    $formKey = $formKey ?? 'create';
    $useOld = old('_form_key') === $formKey;
    $showErrors = $errors->any() && ($useOld || (old('_form_key') === null && $formKey === 'create'));

    $val = function (string $field, mixed $default = '') use ($useOld, $product) {
        if ($useOld) {
            return old($field, $default);
        }

        return $product?->{$field} ?? $default;
    };

    $currentImage = (string) $val('image_url', '/img/cat-dada.png');
    if ($currentImage === '' || str_ends_with($currentImage, '.svg')) {
        $currentImage = '/img/cat-dada.png';
    }
    $previewSrc = str_starts_with($currentImage, 'http') || str_starts_with($currentImage, '/')
        ? $currentImage
        : '/' . ltrim($currentImage, '/');

    $presetImages = [
        '/img/cat-dada.png' => 'Dada',
        '/img/cat-paha.png' => 'Paha',
        '/img/cat-sayap.png' => 'Sayap',
        '/img/cat-ceker.png' => 'Ceker',
        '/img/cat-leher.png' => 'Leher',
        '/img/cat-bakso.png' => 'Bakso',
    ];
    $presetCategories = ['Ayam', 'Camilan', 'Minuman', 'Paket'];
@endphp

<div class="space-y-4" data-product-fields="{{ $formKey }}">
    <input type="hidden" name="_form_key" value="{{ $formKey }}">

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $formKey }}-name" class="block text-xs font-black text-[#4A3B31]">Nama Produk</label>
            <input
                id="{{ $formKey }}-name"
                name="name"
                type="text"
                required
                maxlength="160"
                value="{{ $val('name') }}"
                placeholder="Contoh: Dada Ayam Crispy"
                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
            >
            @if ($showErrors)
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            @endif
        </div>

        <div>
            <label for="{{ $formKey }}-category" class="block text-xs font-black text-[#4A3B31]">Kategori</label>
            <input
                id="{{ $formKey }}-category"
                name="category"
                type="text"
                required
                maxlength="100"
                value="{{ $val('category', 'Ayam') }}"
                placeholder="Ayam / Camilan"
                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
            >
            <div class="mt-1.5 flex flex-wrap gap-1.5">
                @foreach ($presetCategories as $cat)
                    <button
                        type="button"
                        data-set-target="{{ $formKey }}-category"
                        data-set-value="{{ $cat }}"
                        class="rounded-lg border border-[#EBE1D0] bg-[#FAF4EA] px-2 py-0.5 text-[10px] font-black text-[#4A3B31] transition hover:border-[#B83809] hover:text-[#B83809]"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
            @if ($showErrors)
                <x-input-error :messages="$errors->get('category')" class="mt-1" />
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $formKey }}-price" class="block text-xs font-black text-[#4A3B31]">Harga (Rp)</label>
            <input
                id="{{ $formKey }}-price"
                name="price"
                type="number"
                min="0"
                step="500"
                required
                value="{{ $val('price', 7000) }}"
                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-black text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
            >
            @if ($showErrors)
                <x-input-error :messages="$errors->get('price')" class="mt-1" />
            @endif
        </div>

        <div>
            <label for="{{ $formKey }}-stock" class="block text-xs font-black text-[#4A3B31]">Stok Harian</label>
            <input
                id="{{ $formKey }}-stock"
                name="stock"
                type="number"
                min="0"
                required
                value="{{ $val('stock', 20) }}"
                class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-black text-[#1E1915] focus:border-[#B83809] focus:bg-white focus:outline-none"
            >
            @if ($showErrors)
                <x-input-error :messages="$errors->get('stock')" class="mt-1" />
            @endif
        </div>
    </div>

    <div>
        <label for="{{ $formKey }}-description" class="block text-xs font-black text-[#4A3B31]">Deskripsi Menu</label>
        <textarea
            id="{{ $formKey }}-description"
            name="description"
            rows="2"
            required
            placeholder="Jelaskan rasa, porsi, atau tingkat kerenyahan..."
            class="mt-1.5 w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 px-3.5 py-2.5 text-sm font-medium text-[#1E1915] placeholder:text-[#A39382] focus:border-[#B83809] focus:bg-white focus:outline-none"
        >{{ $val('description') }}</textarea>
        @if ($showErrors)
            <x-input-error :messages="$errors->get('description')" class="mt-1" />
        @endif
    </div>

    {{-- Image URL + Preset Picker + File Upload --}}
    <div class="rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/55 p-3.5 space-y-3">
        <div class="flex items-center gap-3">
            <img
                id="{{ $formKey }}-preview"
                src="{{ $previewSrc }}"
                alt="Pratinjau gambar produk"
                class="h-14 w-14 shrink-0 rounded-2xl border border-[#EBE1D0] bg-white object-contain p-1.5 shadow-2xs"
            >
            <div class="min-w-0 flex-1">
                <label for="{{ $formKey }}-image-url" class="block text-xs font-black text-[#4A3B31]">Path / URL Gambar</label>
                <input
                    id="{{ $formKey }}-image-url"
                    name="image_url"
                    type="text"
                    value="{{ $val('image_url', $product?->image_url ?? '') }}"
                    placeholder="/img/cat-dada.png"
                    data-preview-target="{{ $formKey }}-preview"
                    class="mt-1 w-full rounded-xl border border-[#DFD2BD] bg-white px-3 py-1.5 text-xs font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] focus:border-[#B83809] focus:outline-none"
                >
                @if ($showErrors)
                    <x-input-error :messages="$errors->get('image_url')" class="mt-1" />
                @endif
            </div>
        </div>

        <div>
            <p class="text-[11px] font-black text-[#7C6D5D]">Atau pilih ikon menu cepat:</p>
            <div class="mt-1.5 grid grid-cols-3 gap-1.5 sm:grid-cols-6">
                @foreach ($presetImages as $imgPath => $imgLabel)
                    <button
                        type="button"
                        data-pick-image="{{ $imgPath }}"
                        data-url-input="{{ $formKey }}-image-url"
                        data-preview-img="{{ $formKey }}-preview"
                        class="flex flex-col items-center gap-1 rounded-xl border border-[#EBE1D0] bg-white p-1.5 text-[10px] font-extrabold text-[#4A3B31] transition hover:border-[#B83809] hover:bg-[#FFFDF9]"
                    >
                        <img src="{{ asset(ltrim($imgPath, '/')) }}" alt="{{ $imgLabel }}" class="h-7 w-7 object-contain">
                        <span>{{ $imgLabel }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div>
            <label for="{{ $formKey }}-image-file" class="block text-[11px] font-black text-[#4A3B31]">
                Unggah Foto Sendiri (Opsional, maks 5 MB)
            </label>
            <input
                id="{{ $formKey }}-image-file"
                name="image"
                type="file"
                accept="image/*"
                data-file-preview="{{ $formKey }}-preview"
                class="mt-1 block w-full text-xs font-semibold text-[#7C6D5D] file:mr-3 file:rounded-xl file:border-0 file:bg-[#FEF3C7] file:px-3 file:py-1.5 file:text-xs file:font-black file:text-[#92400E] hover:file:bg-[#FDE68A]"
            >
            @if ($showErrors)
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            @endif
        </div>
    </div>
</div>
