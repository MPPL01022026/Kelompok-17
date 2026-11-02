@php
    $activeTab = $activeTab ?? 'home';
@endphp

<div class="sticky bottom-0 z-40 shrink-0 bg-gradient-to-t from-[#FFFDF8] via-[#FFFDF8] to-transparent">
    <div class="px-4 pb-2">
        @include('layouts.navbar-menu')
    </div>

    <nav class="border-t border-[#ECE2D0] bg-white px-6 py-2 shadow-[0_-6px_24px_rgba(34,28,21,0.06)] select-none">
        <div class="flex items-center justify-between">
            {{-- Tab Beranda --}}
            <a href="{{ route('mobile.home') }}"
                class="flex min-w-[84px] flex-col items-center justify-center gap-1 rounded-[20px] px-4 py-2 transition duration-200 {{ $activeTab === 'home' ? 'bg-[#F3B825] text-[#1E1915] font-extrabold shadow-xs' : 'text-[#1E1915] font-semibold hover:bg-[#FFF8E7]' }}">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 10.5L12 3l9 7.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 19.5v-9Z" />
                    <path d="M9 21V13.5a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5V21" />
                </svg>
                <span class="text-xs leading-none">Beranda</span>
            </a>

            {{-- Tab Menu --}}
            <a href="{{ route('mobile.menu') }}"
                class="flex min-w-[84px] flex-col items-center justify-center gap-1 rounded-[20px] px-4 py-2 transition duration-200 {{ $activeTab === 'menu' ? 'bg-[#F3B825] text-[#1E1915] font-extrabold shadow-xs' : 'text-[#1E1915] font-semibold hover:bg-[#FFF8E7]' }}">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2" />
                    <path d="M7 2v20" />
                    <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                </svg>
                <span class="text-xs leading-none">Menu</span>
            </a>

            {{-- Tab Riwayat --}}
            <a href="{{ route('mobile.history') }}"
                class="flex min-w-[84px] flex-col items-center justify-center gap-1 rounded-[20px] px-4 py-2 transition duration-200 {{ $activeTab === 'history' ? 'bg-[#F3B825] text-[#1E1915] font-extrabold shadow-xs' : 'text-[#1E1915] font-semibold hover:bg-[#FFF8E7]' }}">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
                <span class="text-xs leading-none">Riwayat</span>
            </a>
        </div>
    </nav>
</div>
