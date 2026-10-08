@extends('layouts.app')
@section('title', 'Admin | Ayamo')
@section('content')
<main class="login-layout"><section class="login-intro"><a class="brand" href="/"><span class="brand-mark">A</span><span><b>ayamo</b><small>admin workspace</small></span></a><p class="eyebrow">RUANG KERJA AYAMO</p><h1>Jalankan toko<br><em>lebih tenang.</em></h1><p>Kelola produk, pesanan, dan lihat ritme penjualan dalam satu ruang.</p></section><form class="login-form" method="post" action="/admin/login">@csrf<p class="eyebrow">ADMIN LOGIN</p><h2>Selamat datang.</h2><label>Username<input name="username" autocomplete="username" required></label><label>Password<input name="password" type="password" autocomplete="current-password" required></label><button class="button" type="submit">Masuk ke dashboard</button><a class="back-link" href="/">← Kembali ke katalog</a></form></main>
@endsection
