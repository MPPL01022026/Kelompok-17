@extends('layouts.mobile')
@section('title', 'AYAMO | Eat. Chill. Repeat')
@section('hideStatusBar', true)
@section('content')
    <div id="splash-container"
        class="relative flex min-h-screen w-full flex-1 flex-col items-center justify-center overflow-hidden bg-gradient-to-b from-[#F98E1D] via-[#F59E0B] to-[#FBBF24] px-6 text-center select-none">

        {{-- Decorative Organic Shapes matching Tampilan Awal.png & initialization.png --}}
        <div class="pointer-events-none absolute -top-8 -right-16 h-72 w-80 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-28 -left-24 h-72 w-72 rounded-full bg-white/6"></div>
        <div class="pointer-events-none absolute -bottom-24 left-12 h-[360px] w-[300px] rounded-full bg-white/12"></div>

        {{-- Logo Box --}}
        <div class="relative z-10 flex h-28 w-28 items-center justify-center rounded-[28px] bg-white p-2.5 shadow-[0_10px_28px_rgba(124,45,18,0.18)]">
            <img src="{{ asset('img/logo.png') }}" alt="Ayamo Logo" class="h-full w-full rounded-2xl object-contain">
        </div>

        {{-- Brand Title & Tagline --}}
        <h1 class="relative z-10 mt-5 font-['Outfit'] text-[32px] font-extrabold tracking-wide text-white drop-shadow-2xs">AYAMO</h1>
        <p class="relative z-10 mt-0.5 text-sm font-medium tracking-[0.14em] text-white/95">EAT. CHILL. REPEAT</p>

        {{-- Initialization Loading Circle --}}
        <div id="init-loader" class="relative z-10 mt-14 flex h-18 w-18 items-center justify-center rounded-full bg-[#EAF4F8] shadow-md">
            <svg class="h-9 w-9 animate-spin" viewBox="0 0 36 36" fill="none">
                <circle cx="18" cy="18" r="13" stroke="#DCE5EA" stroke-width="3.5" />
                <path d="M27.5 27 A13 13 0 0 1 9 27" stroke="#EA8C06" stroke-width="3.5" stroke-linecap="round" />
            </svg>
        </div>

        {{-- Enter Button --}}
        <a href="{{ route('mobile.home') }}"
            class="relative z-10 mt-10 rounded-full bg-white/25 px-6 py-2 text-xs font-bold text-white backdrop-blur-xs transition hover:bg-white/35">
            Masuk ke Beranda →
        </a>

        {{-- Bottom Home Indicator --}}
        <div class="absolute bottom-2.5 left-1/2 h-1 w-32 -translate-x-1/2 rounded-full bg-[#524E4A]"></div>
    </div>

    <script>
        setTimeout(() => {
            window.location.href = @json(route('mobile.home'));
        }, 2200);
    </script>
@endsection
