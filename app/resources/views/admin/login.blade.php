@extends('layouts.app')

@section('title', 'Masuk Admin — Ayamo')

@section('content')
<div class="min-h-screen bg-[#FAF4EA] text-[#1E1915]">
    <main id="main-content" tabindex="-1" class="!grid !min-h-screen !max-w-none !grid-cols-1 !p-0 lg:!grid-cols-[minmax(0,1.08fr)_minmax(440px,.92fr)]">
        {{-- Left Brand & Story Panel --}}
        <section class="relative flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#B83809] via-[#9E2E06] to-[#681D03] p-8 text-white sm:p-12 lg:p-14">
            {{-- Subtle Warm Culinary Glow --}}
            <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-[#F59E0B]/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-20 h-96 w-96 rounded-full bg-[#FDE047]/10 blur-3xl"></div>

            {{-- Brand Top --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('web.home') }}" class="inline-flex items-center gap-3.5 rounded-2xl bg-white/10 px-4 py-2.5 backdrop-blur-sm transition hover:bg-white/15">
                    <img src="{{ asset('img/logo.webp') }}" alt="Logo Ayamo" class="h-10 w-10 rounded-xl bg-[#FFFDF9] p-1 object-contain shadow-xs">
                    <div>
                        <p class="font-['Outfit'] text-lg font-black leading-none tracking-tight text-white">AYAMO</p>
                        <p class="mt-0.5 text-[11px] font-bold tracking-wide text-[#FEF08A]">ADMIN CONSOLE</p>
                    </div>
                </a>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-extrabold text-[#FEF08A]">
                    <span class="h-2 w-2 rounded-full bg-[#4ADE80]"></span>
                    Panel Dapur &amp; Kasir
                </span>
            </div>

            {{-- Center Hero Copy & Visual Preview --}}
            <div class="relative z-10 my-10 lg:my-0">
                <span class="inline-flex items-center rounded-full bg-[#F59E0B] px-3.5 py-1 text-xs font-black tracking-wide text-[#1E1915] shadow-xs">
                    PANEL ADMIN AYAMO
                </span>

                <h1 class="mt-5 max-w-xl font-['Outfit'] text-3xl font-black leading-[1.12] tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Kelola pesanan, stok, dan laporan dalam satu layar.
                </h1>
                <p class="mt-4 max-w-lg text-sm font-medium leading-relaxed text-white/85 sm:text-base">
                    Proses pesanan Web &amp; Mobile bertahap, atur stok menu, dan catat kasir harian.
                </p>

                {{-- Feature Preview Card --}}
                <div class="mt-8 grid max-w-xl gap-4 rounded-[28px] border border-white/15 bg-white/10 p-5 backdrop-blur-md sm:grid-cols-[auto_1fr] sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-[#FFFDF9] to-[#F6EBD8] p-2 shadow-md">
                        <img src="{{ asset('img/hero-chicken-mockup.png') }}" alt="Menu Ayamo Crispy" class="h-full w-full object-contain">
                    </div>
                    <div class="grid grid-cols-1 gap-2 text-xs font-bold text-white/90 sm:grid-cols-2">
                        <div class="flex items-center gap-2 rounded-xl bg-black/15 px-3 py-2">
                            <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#FDE047]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Proses 4 Tahap Pesanan</span>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl bg-black/15 px-3 py-2">
                            <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#FDE047]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Auto Kembalikan Stok Batal</span>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl bg-black/15 px-3 py-2">
                            <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#FDE047]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Kasir Manual Cepat</span>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl bg-black/15 px-3 py-2">
                            <svg aria-hidden="true" class="h-4 w-4 shrink-0 text-[#FDE047]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Atur Jam &amp; Rekening Toko</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Links --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 border-t border-white/15 pt-5 text-xs font-bold text-white/80">
                <a href="{{ route('web.home') }}" class="inline-flex items-center gap-1.5 transition hover:text-[#FEF08A]">
                    <span>←</span>
                    <span>Kembali ke Toko Web</span>
                </a>
                <a href="{{ route('mobile.home') }}" class="inline-flex items-center gap-1.5 transition hover:text-[#FEF08A]">
                    <span>Pratinjau Toko Mobile</span>
                    <span>↗</span>
                </a>
            </div>
        </section>

        {{-- Right Login Form Panel --}}
        <section class="flex items-center justify-center bg-[#FAF4EA] p-6 sm:p-10 lg:p-14">
            <div class="w-full max-w-[440px] rounded-[2rem] border border-[#EBE1D0] bg-[#FFFDF9] p-7 shadow-[0_24px_60px_-24px_rgba(30,25,21,0.16)] sm:p-9">
                <div class="flex items-center justify-between gap-3">
                    <span class="rounded-full bg-[#FEF3C7] px-3 py-1 text-[11px] font-black tracking-wide text-[#92400E]">
                        AKSES PENGELOLA
                    </span>
                    <span class="text-xs font-bold text-[#7C6D5D]">Ayamo Kitchen</span>
                </div>

                <h2 class="mt-4 font-['Outfit'] text-2xl font-black tracking-tight text-[#1E1915] sm:text-3xl">
                    Masuk ke Panel Admin
                </h2>
                <p class="mt-1.5 text-sm font-medium text-[#7C6D5D]">
                    Masukkan kredensial admin untuk mengelola pesanan dan stok toko hari ini.
                </p>

                <form action="/admin/login" method="post" class="mt-7 space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="block text-xs font-black tracking-wide text-[#4A3B31]">
                            Username
                        </label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#9A8B7A]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </span>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                required
                                autofocus
                                autocomplete="username"
                                value="{{ old('username') }}"
                                placeholder="admin"
                                class="w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 py-3 pr-4 pl-10 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] transition focus:border-[#B83809] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#F59E0B]/30"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="admin-password" class="block text-xs font-black tracking-wide text-[#4A3B31]">
                            Kata Sandi
                        </label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#9A8B7A]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            <input
                                id="admin-password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA]/60 py-3 pr-12 pl-10 text-sm font-bold text-[#1E1915] placeholder:font-medium placeholder:text-[#A39382] transition focus:border-[#B83809] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#F59E0B]/30"
                            >
                            <button
                                type="button"
                                onclick="(function(btn){ const input = document.getElementById('admin-password'); if (!input) return; const hidden = input.type === 'password'; input.type = hidden ? 'text' : 'password'; btn.setAttribute('aria-pressed', hidden ? 'true' : 'false'); btn.textContent = hidden ? 'Tutup' : 'Lihat'; })(this)"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-3 my-auto h-fit rounded-lg px-2 py-1 text-[11px] font-black text-[#7C6D5D] transition hover:bg-[#F3E7D3] hover:text-[#1E1915]"
                            >
                                Lihat
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#F59E0B] px-5 py-3.5 font-['Outfit'] text-sm font-black text-[#1E1915] shadow-[0_12px_24px_-12px_rgba(245,158,11,0.8)] transition hover:bg-[#D97706] active:scale-[0.99]"
                    >
                        <span>Masuk ke Dashboard</span>
                        <span>→</span>
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-between border-t border-[#EBE1D0] pt-4 text-xs font-semibold text-[#7C6D5D]">
                    <span>Sesi dilindungi cookie terenkripsi</span>
                    <a href="{{ route('web.home') }}" class="font-black text-[#B83809] hover:underline">
                        Toko Web
                    </a>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
