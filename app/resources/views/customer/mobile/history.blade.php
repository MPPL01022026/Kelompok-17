@extends('layouts.mobile')
@section('title', 'Riwayat | Ayamo Mobile')
@section('content')
    @php
        $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
    @endphp

    <main class="flex-1 px-4 pt-3 pb-32">
        <div>
            <header class="mb-5 pt-1">
                <h1 class="font-['Outfit'] text-2xl font-extrabold text-[#1E1915]">Riwayat</h1>
            </header>

            <div class="space-y-4">
                @forelse ($orders as $order)
                    @php
                        $isDone = in_array($order->status, ['confirmed', 'completed'], true);
                        $createdAt = $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at) : null;
                        $dateLabel = $createdAt ? $createdAt->format('j') . ' ' . ($months[(int) $createdAt->format('n')] ?? 'Okt') : '8 Okt';
                    @endphp
                    <article class="rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-4 transition hover:shadow-xs">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="font-['Outfit'] text-base font-extrabold text-[#1E1915]">{{ $order->code }}</h2>
                            @if ($isDone)
                                <span class="rounded-full bg-[#D8F9E5] px-4 py-1 text-xs font-extrabold text-[#15803D]">
                                    Selesai
                                </span>
                            @else
                                <span class="rounded-full bg-[#FCECD8] px-4 py-1 text-xs font-extrabold text-[#9A3412]">
                                    Menunggu
                                </span>
                            @endif
                        </div>

                        <p class="mt-1 text-xs font-medium text-[#2C241D]">
                            {{ $dateLabel }} &middot; {{ $order->items_summary ?: '1x Dada Ayam' }}
                        </p>

                        <strong class="mt-4 block font-['Outfit'] text-base font-extrabold text-[#1E1915]">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </strong>

                        <div class="mt-3 flex items-center justify-end gap-2.5">
                            <a href="{{ route('mobile.orders.status', $order->id) }}"
                                class="rounded-2xl border-[1.4px] border-[#3D312A] bg-[#F2F2F2] px-6 py-2 font-['Outfit'] text-xs font-extrabold text-[#1E1915] transition hover:bg-[#E5E5E5] active:scale-95">
                                Detail
                            </a>

                            <form method="post" action="{{ route('mobile.orders.reorder', $order->id) }}">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer rounded-2xl bg-[#F5CB42] px-5 py-2 font-['Outfit'] text-xs font-extrabold text-[#1E1915] transition hover:bg-[#EAB308] active:scale-95">
                                    Pesan Lagi
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[22px] border-[1.4px] border-[#3D312A] bg-[#FFFDF9] p-8 text-center">
                        <p class="text-sm font-bold text-[#645943]">Belum ada riwayat pesanan.</p>
                    </div>
                @endforelse
            </div>

            <p class="mt-3.5 text-center text-xs font-extrabold text-[#1E1915]">
                Seluruh riwayat tersimpan di perangkat ini
            </p>
        </div>
    </main>

    @include('layouts.navbar', ['activeTab' => 'history'])
@endsection
