@extends('layouts.app')

@section('title', match ($tab) {
    'orders' => 'Pesanan Masuk — Admin Ayamo',
    'products' => 'Katalog Produk — Admin Ayamo',
    'reports' => 'Laporan UMKM — Admin Ayamo',
    'settings' => 'Pengaturan Toko — Admin Ayamo',
    default => 'Ringkasan Toko — Admin Ayamo',
})

@section('content')
@php
    $navItems = [
        [
            'key' => 'overview',
            'label' => 'Ringkasan',
            'badge' => null,
            'badgeClass' => '',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        ],
        [
            'key' => 'orders',
            'label' => 'Pesanan',
            'badge' => $stats['pending_orders'] > 0 ? $stats['pending_orders'] . ' baru' : null,
            'badgeClass' => 'bg-[#FEF3C7] text-[#92400E]',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />',
        ],
        [
            'key' => 'products',
            'label' => 'Produk',
            'badge' => $stats['low_stock'] > 0 ? $stats['low_stock'] . ' menipis' : null,
            'badgeClass' => 'bg-[#FEE2E2] text-[#991B1B]',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />',
        ],
        [
            'key' => 'reports',
            'label' => 'Laporan',
            'badge' => null,
            'badgeClass' => '',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />',
        ],
        [
            'key' => 'settings',
            'label' => 'Pengaturan',
            'badge' => null,
            'badgeClass' => '',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        ],
    ];
@endphp

<div class="min-h-screen bg-[#FAF4EA] text-[#1E1915] print:bg-white">
    {{-- Mobile / Tablet Header Bar --}}
    <header class="sticky top-0 z-30 border-b border-[#EBE1D0] bg-[#FFFDF9]/95 px-4 py-3 backdrop-blur-md lg:hidden print:hidden">
        <div class="mx-auto flex max-w-[1540px] items-center justify-between gap-3">
            <a href="/admin" class="flex items-center gap-2.5">
                <img src="{{ asset('img/logo.webp') }}" alt="Logo Ayamo" class="h-9 w-9 rounded-xl object-contain">
                <div>
                    <p class="font-['Outfit'] text-base font-black leading-none text-[#B83809]">{{ $settings['store_name'] ?? 'Ayamo' }}</p>
                    <p class="mt-0.5 text-[10px] font-extrabold text-[#7C6D5D]">Panel Admin</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('web.home') }}" target="_blank" rel="noopener" class="rounded-full border border-[#EBE1D0] bg-[#FAF4EA] px-3 py-1.5 text-xs font-extrabold text-[#4A3B31]">
                    Web ↗
                </a>
                <form action="/admin/logout" method="post">
                    @csrf
                    <button type="submit" class="rounded-full bg-[#1E1915] px-3.5 py-1.5 text-xs font-extrabold text-white">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <nav aria-label="Navigasi Admin Mobile" class="mt-3 flex gap-2 overflow-x-auto pb-1">
            @foreach ($navItems as $item)
                @php
                    $active = $tab === $item['key'];
                @endphp
                <a href="/admin?tab={{ $item['key'] }}" @if ($active) aria-current="page" @endif class="inline-flex shrink-0 items-center gap-2 rounded-full border px-3.5 py-1.5 text-xs font-extrabold transition {{ $active ? 'border-[#B83809] bg-[#B83809] text-white shadow-2xs' : 'border-[#EBE1D0] bg-white text-[#4A3B31]' }}">
                    <span>{{ $item['label'] }}</span>
                    @if ($item['badge'])
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $active ? 'bg-white/20 text-white' : $item['badgeClass'] }}">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>
    </header>

    <main id="main-content" tabindex="-1" class="!mx-auto !w-full !max-w-[1540px] !px-4 !pt-6 !pb-20 sm:!px-6 lg:!px-10 lg:!pt-8 xl:!px-12 print:!max-w-none print:!p-0">
        <div class="flex flex-col gap-7 lg:flex-row lg:items-start">
            {{-- Desktop Sidebar --}}
            <aside class="hidden w-[276px] shrink-0 space-y-5 lg:sticky lg:top-8 lg:block print:hidden">
                {{-- Brand & Store Card --}}
                <div class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-5 shadow-2xs">
                    <a href="/admin" class="flex items-center gap-3.5">
                        <img src="{{ asset('img/logo.webp') }}" alt="Logo Ayamo" class="h-11 w-11 rounded-2xl object-contain shadow-2xs">
                        <div class="min-w-0">
                            <p class="truncate font-['Outfit'] text-xl font-black tracking-tight text-[#B83809]">
                                {{ $settings['store_name'] ?? 'Ayamo' }}
                            </p>
                            <p class="text-[11px] font-extrabold tracking-wide text-[#7C6D5D]">
                                ADMIN WORKSPACE
                            </p>
                        </div>
                    </a>

                    @php $storeStatus = \App\Models\Settings::getStoreStatus($settings ?? null); @endphp
                    <div class="mt-4 space-y-2 rounded-2xl border border-[#EBE1D0] bg-[#FAF4EA]/75 p-3.5 text-xs">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 font-extrabold {{ $storeStatus['is_open'] ? 'text-[#166534]' : 'text-[#991B1B]' }}">
                                <span class="h-2 w-2 rounded-full {{ $storeStatus['is_open'] ? 'bg-[#22C55E]' : 'bg-[#EF4444]' }}"></span>
                                {{ $storeStatus['label'] }}
                            </span>
                            <span class="font-black text-[#1E1915]">{{ $settings['store_time'] ?? '15.00 - 03.00' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[#7C6D5D]">
                            <span class="font-semibold">Menu aktif</span>
                            <span class="font-black text-[#1E1915]">{{ $stats['active_products'] }} varian</span>
                        </div>
                        @if ($stats['processing_orders'] > 0)
                            <div class="flex items-center justify-between gap-2 text-[#9A3412]">
                                <span class="font-semibold">Sedang diproses</span>
                                <span class="font-black">{{ $stats['processing_orders'] }} pesanan</span>
                            </div>
                        @endif
                    </div>

                    {{-- Navigation --}}
                    <nav class="mt-5 space-y-1.5" aria-label="Navigasi Admin">
                        @foreach ($navItems as $item)
                            @php
                                $active = $tab === $item['key'];
                            @endphp
                            <a
                                href="/admin?tab={{ $item['key'] }}"
                                @if ($active) aria-current="page" @endif
                                class="group flex items-center justify-between gap-2 rounded-2xl px-3.5 py-2.5 text-sm font-extrabold transition {{ $active ? 'bg-gradient-to-r from-[#B83809] to-[#C84612] text-white shadow-[0_10px_20px_-12px_rgba(184,56,9,0.7)]' : 'text-[#4A3B31] hover:bg-[#FAF4EA] hover:text-[#1E1915]' }}"
                            >
                                <span class="flex items-center gap-3">
                                    <svg class="h-4.5 w-4.5 {{ $active ? 'text-[#FEF08A]' : 'text-[#7C6D5D] group-hover:text-[#B83809]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span>{{ $item['label'] }}</span>
                                </span>
                                @if ($item['badge'])
                                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black {{ $active ? 'bg-white/20 text-white' : $item['badgeClass'] }}">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>

                {{-- Storefront Links & Logout --}}
                <div class="rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-4 shadow-2xs space-y-2.5">
                    <p class="px-1 text-[11px] font-black tracking-wide text-[#7C6D5D] uppercase">
                        Pratinjau Toko
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('web.home') }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1 rounded-xl border border-[#EBE1D0] bg-[#FAF4EA] px-3 py-2 text-xs font-extrabold text-[#1E1915] transition hover:border-[#B83809]/40 hover:bg-white">
                            <span>Web</span>
                            <span class="text-[#B83809]">↗</span>
                        </a>
                        <a href="{{ route('mobile.home') }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1 rounded-xl border border-[#EBE1D0] bg-[#FAF4EA] px-3 py-2 text-xs font-extrabold text-[#1E1915] transition hover:border-[#B83809]/40 hover:bg-white">
                            <span>Mobile</span>
                            <span class="text-[#B83809]">↗</span>
                        </a>
                    </div>

                    <form action="/admin/logout" method="post" class="pt-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-[#E5D7C3] bg-white px-4 py-2.5 text-xs font-black text-[#991B1B] transition hover:border-[#FECACA] hover:bg-[#FEF2F2]">
                            <span>Keluar dari Admin</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main Workspace --}}
            <div class="min-w-0 flex-1 space-y-6">
                @if (session('status'))
                    <div role="status" class="flex items-center justify-between gap-3 rounded-2xl border border-[#BBF7D0] bg-[#DCFCE7] px-5 py-3.5 text-sm font-extrabold text-[#166534] shadow-2xs print:hidden">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#166534] text-white">
                                <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                            <span>{{ session('status') }}</span>
                        </div>
                        <span class="text-[11px] font-bold text-[#15803D]">Tersimpan</span>
                    </div>
                @endif

                @include("admin.partials.{$tab}")
            </div>
        </div>
    </main>
</div>
@endsection
