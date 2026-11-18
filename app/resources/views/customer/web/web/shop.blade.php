@extends('layouts.app')
@section('title', 'Ayamo | Ayam Crispy Favorit')
@section('content')
  <script>
    (function() {
      function checkMobileViewport() {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (isStandalone || window.innerWidth <= 768) {
          window.location.replace('/mobile');
        }
      }
      checkMobileViewport();
      window.addEventListener('resize', checkMobileViewport);
    })();
  </script>
  <header class="site-header">
    <a class="brand" href="/"><span class="brand-mark">A</span><span><b>ayamo</b><small>fried chicken, renyah tiap
          hari</small></span></a>
    <nav><a href="#menu">Katalog</a><a href="/mobile">Mobile UI</a><a href="/admin">Admin</a><a class="cart-link" href="#keranjang">Keranjang
        <b>{{ array_sum($cart) }}</b></a></nav>
  </header>
  <main>
    <section class="hero">
      <div>
        <p class="eyebrow">AYAMO FRIED CHICKEN</p>
        <h1>Renyah di luar,<br><em>juicy di dalam.</em></h1>
        <p class="hero-desc">Menu ayam goreng crispy dengan bumbu rahasia Ayamo. Pesan sekarang, ambil di tempat.</p><a
          class="button" href="#menu">Lihat menu <span>→</span></a>
      </div>
      <div class="hero-photo"><img src="{{ asset('img/hero.jpg') }}" alt="Ayam crispy Ayamo"><span>Digoreng
          fresh<br><b>setiap pesanan</b></span></div>
    </section>
    <section class="catalog" id="menu">
      <div class="section-head">
        <div>
          <p class="eyebrow">MENU FAVORIT</p>
          <h2>Renyah gurih,<br><span>bikin nagih.</span></h2>
        </div>
        <p>{{ $products->count() }} pilihan menu</p>
      </div>
      <div class="product-grid">
        @foreach ($products as $product)
          <article class="product-card">
            <div class="product-image"><img
                src="{{ str_starts_with($product->image_url, '/') ? asset(ltrim($product->image_url, '/')) : $product->image_url }}"
                alt="{{ $product->name }}"><span
                class="stock">{{ $product->stock > 0 ? $product->stock . ' tersedia' : 'Habis' }}</span></div>
            <div class="product-info">
              <p class="category">{{ $product->category }}</p>
              <h3>{{ $product->name }}</h3>
              <p class="desc">{{ $product->description }}</p>
              <div class="buy-row"><strong>Rp{{ number_format($product->price, 0, ',', '.') }}</strong>
                <form method="post" action="/cart/add">@csrf<input type="hidden" name="product_id"
                    value="{{ $product->id }}"><button class="button small" @disabled($product->stock < 1)>+ Tambah</button>
                </form>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </section>
    <section class="checkout-section" id="keranjang">
      <div>
        <p class="eyebrow">PESANAN ANDA</p>
        <h2>Keranjang</h2>
        <p class="subtle">Atur jumlah, lalu isi data untuk menyelesaikan pesanan.</p>
      </div>
      @if ($cartProducts->isEmpty())
        <p class="empty">Keranjang masih kosong. Pilih menu favoritmu dulu.</p>
      @else
        <form class="checkout-form" method="post" action="/checkout">@csrf
          <div class="cart-lines">
            @foreach ($cart as $id => $quantity)
              @if ($cartProducts->has($id))
                <label
                  class="cart-line"><span>{{ $cartProducts[$id]->name }}<small>Rp{{ number_format($cartProducts[$id]->price, 0, ',', '.') }}</small></span><input
                    aria-label="Jumlah {{ $cartProducts[$id]->name }}" type="number" min="0"
                    max="{{ $cartProducts[$id]->stock }}" name="quantities[{{ $id }}]" value="{{ $quantity }}"></label>
              @endif
            @endforeach
            <button formaction="/cart/update" formmethod="post" class="button secondary">Perbarui keranjang</button>
          </div>
          <label>Nama pemesan<input name="customer_name" value="{{ old('customer_name') }}" required></label>
          <label>No. WhatsApp<input name="phone" value="{{ old('phone') }}" required></label>
          <label>Catatan<textarea name="note" rows="2">{{ old('note') }}</textarea></label>
          <label>Metode pemesanan<select name="payment_method">
              <option value="cod">Bayar di tempat</option>
              <option value="whatsapp">Lanjut melalui WhatsApp</option>
            </select></label>
          <button class="button" type="submit">Kirim pesanan</button>
        </form>
      @endif
    </section>
  </main>
  <footer>Ayamo <span>·</span> Sajian hangat, dibuat saat dipesan.</footer>
@endsection
