@extends('layouts.app')
@section('title', 'Admin Ayamo')
@section('content')
    <div class="admin-layout">
        <aside class="admin-side"><a class="brand" href="/"><span class="brand-mark">A</span><span><b>ayamo</b><small>admin
                        workspace</small></span></a>
            <nav><a class="{{ $tab === 'overview' ? 'active' : '' }}" href="/admin?tab=overview">Ringkasan</a><a
                    class="{{ $tab === 'orders' ? 'active' : '' }}" href="/admin?tab=orders">Pesanan
                    <b>{{ $orders->where('status', 'pending')->count() }}</b></a><a
                    class="{{ $tab === 'products' ? 'active' : '' }}" href="/admin?tab=products">Produk</a></nav>
            <form method="post" action="/admin/logout">@csrf<button class="logout">Keluar</button></form>
        </aside>
        <main class="admin-main">
            <header class="admin-head">
                <div>
                    <p class="eyebrow">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <h1>{{ ['overview' => 'Ringkasan penjualan', 'orders' => 'Pesanan masuk', 'products' => 'Katalog produk'][$tab] }}
                    </h1>
                </div><span class="admin-avatar">{{ strtoupper(substr(session('admin'), 0, 1)) }}
                    <b>{{ session('admin') }}</b></span>
            </header>
            @if ($tab === 'overview')
                <section class="metrics">
                    <article><span>Total
                            omzet</span><strong>Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</strong><small>Transaksi
                            tercatat</small></article>
                    <article><span>Produk terjual</span><strong>{{ $stats['units'] }} <small>pcs</small></strong><small>Semua
                            periode</small></article>
                    <article><span>Transaksi selesai</span><strong>{{ $stats['transactions'] }}</strong><small>Online &
                            manual</small></article>
                </section>
                <section class="admin-columns">
                    <article class="panel">
                        <div class="panel-heading">
                            <div>
                                <p class="eyebrow">PERFORMA</p>
                                <h2>Omzet {{ ['day' => 'harian', 'week' => 'mingguan', 'month' => 'bulanan'][$period] }}</h2>
                            </div>
                            <nav class="periods">
                                @foreach (['day' => 'Harian', 'week' => 'Mingguan', 'month' => 'Bulanan'] as $key => $label)<a
                                    class="{{ $period === $key ? 'active' : '' }}"
                                href="/admin?period={{ $key }}">{{ $label }}</a>@endforeach</nav>
                        </div>
                        @forelse ($stats['chart'] as $row)
                            <div class="chart-row"><span>{{ $row['label'] }}</span>
                                <div><i
                                        style="width: {{ max(2, $stats['revenue'] ? min(100, $row['revenue'] / $stats['revenue'] * 100) : 2) }}%"></i>
                                </div><strong>Rp{{ number_format($row['revenue'], 0, ',', '.') }}</strong>
                        </div>@empty<p class="empty">Belum ada transaksi terkonfirmasi.</p>@endforelse
                    </article>
                    <article class="panel">
                        <div class="panel-heading">
                            <div>
                                <p class="eyebrow">PESANAN TERBARU</p>
                                <h2>Masuk hari ini</h2>
                            </div><a href="/admin?tab=orders">Lihat semua →</a>
                        </div>
                        @forelse ($orders->take(5) as $order)
                            <div class="order-mini">
                                <span><b>{{ $order->customer_name }}</b><small>{{ count(json_decode($order->items, true) ?: []) }}
                                        item · Rp{{ number_format($order->total, 0, ',', '.') }}</small></span><em
                                    class="status {{ $order->status }}">{{ $order->status === 'pending' ? 'Baru' : 'Selesai' }}</em>
                        </div>@empty<p class="empty">Belum ada pesanan.</p>@endforelse
                    </article>
                </section>
                <section class="panel manual-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">PENJUALAN</p>
                            <h2>Catat transaksi manual</h2>
                        </div>
                    </div>
                    <form class="inline-form" method="post" action="/admin/transactions">@csrf<label>Nama pelanggan<input
                                name="customer_name" required></label><label>Produk<select
                                name="product_id">@foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ·
                                Rp{{ number_format($product->price, 0, ',', '.') }}</option>@endforeach
                            </select></label><label>Jumlah<input type="number" name="quantity" value="1" min="1"
                                required></label><button class="button">Simpan transaksi</button></form>
                </section>
            @elseif ($tab === 'orders')
                <section class="panel table-panel">
                    <div class="table-head"><span>Pelanggan</span><span>Pesanan</span><span>Total</span><span>Status</span>
                    </div>
                    @forelse ($orders as $order)
                        <div class="table-row">
                            <div><b>{{ $order->customer_name }}</b><small>{{ $order->phone }} ·
                                    {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</small></div>
                            <span>{{ collect(json_decode($order->items, true) ?: [])->map(fn($item) => $item['name'] . ' (' . $item['quantity'] . 'x)')->join(', ') }}</span><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                            <div>@if ($order->status === 'pending')
                                <form method="post" action="/admin/orders/{{ $order->id }}/confirm">@csrf<button
                            class="button small">Konfirmasi</button></form>@else<span
                                        class="status confirmed">Selesai</span>@endif
                            </div>
                    </div>@empty<p class="empty">Belum ada pesanan pelanggan.</p>@endforelse
                </section>
            @else
                <section class="panel table-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">INVENTORI</p>
                            <h2>Tambah produk</h2>
                        </div>
                    </div>
                    <form class="product-form" method="post" action="/admin/products" enctype="multipart/form-data">
                        @csrf@include('admin.product-fields')<button class="button">Tambah produk</button></form>
                </section>
                <section class="panel product-list">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">KATALOG</p>
                            <h2>Produk aktif</h2>
                        </div>
                    </div>
                    @foreach ($products as $product)
                        <details class="product-edit">
                            <summary><img
                                    src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}"
                                    alt=""><span><b>{{ $product->name }}</b><small>{{ $product->category }} · {{ $product->stock }}
                                        stok</small></span><strong>Rp{{ number_format($product->price, 0, ',', '.') }}</strong>
                            </summary>
                            <form class="product-form" method="post" action="/admin/products/{{ $product->id }}"
                                enctype="multipart/form-data">@csrf@include('admin.product-fields', ['product' => $product])<button
                                    class="button small">Simpan perubahan</button></form>
                            <form method="post" action="/admin/products/{{ $product->id }}/delete"
                                onsubmit="return confirm('Arsipkan produk ini?')">@csrf<button class="text-danger">Arsipkan
                                    produk</button></form>
                    </details>@endforeach
                </section>
            @endif
        </main>
    </div>
@endsection
