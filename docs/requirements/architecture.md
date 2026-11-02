# 🏗️ Arsitektur Sistem Ayamo

Dokumen ini menjelaskan fondasi arsitektur, tumpukan teknologi (tech stack), serta alur kerja teknis aplikasi web **Ayamo**.

---

## 1. Tumpukan Teknologi (Tech Stack)

Sistem Ayamo memanfaatkan arsitektur modern berbasis Laravel 12 dan Tailwind CSS:

```mermaid
graph TD
    Client[Browser Client / Kasir UI]
    Tailwind[Tailwind CSS v4 Design Tokens]
    Blade[Blade Components & Views]
    Controllers[Modular Http Controllers]
    Models[Eloquent Models ORM]
    DB[(MySQL / MariaDB)]

    Client --> Tailwind
    Client --> Blade
    Blade <--> Controllers
    Controllers <--> Models
    Models <--> DB
```

### Rincian Komponen:
1. **Laravel 12 (Core Backend)**
   - Mengatur alur bisnis, routing modular, validasi request, sesi admin, dan interaksi basis data Eloquent ORM.
   - Menggunakan PHP 8.4+ dengan fitur tipe data ketat (*strict types* & *constructor property promotion*).
2. **Modular Controllers**
   - Pemisahan tanggung jawab controller per domain fungsional:
     - `Customer\Mobile*Controller`: katalog, keranjang, checkout, pembayaran, status, dan riwayat untuk tampilan mobile.
     - `Customer\Web*Controller`: controller modular untuk alur customer web dengan aturan bisnis bersama dari controller mobile/storefront.
     - `Admin\AuthController`: Otentikasi login/logout admin & proteksi brute-force.
     - `Admin\DashboardController`: Metrik omzet penjualan, grafik analitik, produk terlaris.
     - `Admin\OrderController`: Konfirmasi pesanan tanpa mengurangi ulang stok yang sudah direservasi saat checkout.
     - `Admin\ProductController`: CRUD katalog produk & pengarsipan.
     - `Admin\TransactionController`: Pencatatan transaksi kasir manual langsung di dashboard.
3. **Eloquent ORM Models**
   - `Product`: Master produk, harga, stok, dan helper seeding default.
   - `Order`: Pesanan pelanggan online dengan payload item JSON.
   - `Transaction`: Rekap mutasi transaksi penjualan terkonfirmasi.
   - `LoginAttempt`: Pelacakan percobaan login gagal & lockout throttle.
   - `User`: Model pengguna/admin aplikasi.
4. **Tailwind CSS v4 & Theme Design System**
   - Mesin styling modern menggunakan variabel CSS dan `@theme` dengan token warna kuliner warm appetizing (*Gold, Orange, Sand, Green, Red, Sky*), dark mode switcher, serta badge level kepedasan (*Spice Level*).
5. **MySQL / MariaDB**
   - Basis data relasional penyimpan data pengguna, katalog menu, pesanan, detail transaksi kasir, dan riwayat stok.

---

## 2. Struktur Modul Aplikasi (`app/`)

```text
app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AuthController.php          # Login, logout, lockout throttle
│   │   │   │   ├── DashboardController.php     # Metrik omzet, grafik & best seller
│   │   │   │   ├── OrderController.php         # Konfirmasi pesanan & update stok
│   │   │   │   ├── ProductController.php       # Simpan & arsipkan produk
│   │   │   │   └── TransactionController.php   # Catat transaksi kasir manual
│   │   │   ├── Customer/
│   │   │   │   ├── MobileStorefrontController.php # Basis aturan katalog & keranjang
│   │   │   │   ├── Mobile*Controller.php       # Alur customer mobile
│   │   │   │   └── Web*Controller.php          # Alur customer web
│   │   │   └── Controller.php                  # Base controller abstrak
│   │   └── Requests/                           # Request validation
│   ├── Models/
│   │   ├── LoginAttempt.php                    # Model pelacak login lockout
│   │   ├── Order.php                           # Model pesanan pelanggan
│   │   ├── Product.php                         # Model katalog produk & stok
│   │   ├── Transaction.php                     # Model mutasi transaksi
│   │   └── User.php                            # Model akun user
│   └── Providers/                              # Service providers
├── database/
│   ├── factories/                              # Model factories untuk testing
│   ├── migrations/                             # Skema tabel database
│   └── seeders/                                # Data awal (seeder menu, admin, dsb.)
├── resources/
│   ├── css/
│   │   └── app.css                             # Setup Tailwind CSS v4 & theme tokens
│   ├── js/
│   │   └── app.js                              # Inisialisasi script frontend
│   └── views/
│       ├── admin/                              # View panel admin (dashboard, login, forms)
│       ├── components/                         # Reusable Blade components (<x-input-error>, dll.)
│       ├── layouts/                            # Layout template (app, guest)
│       └── customer/                           # View customer web dan mobile
└── routes/
    └── web.php                                 # Routing publik & middleware admin
```

---

## 3. Pola Interaksi Data & State Management

### A. Alur Pemesanan Online (Customer Journey)
1. **Etalase Menu**: Pelanggan memilih menu melalui `WebCatalogController` atau `MobileCatalogController`.
2. **Keranjang Lokal**: Penambahan item disimpan pada `localStorage` (`ayamo_cart_v1`) sehingga klik **Tambah** tidak mengirim request. Keranjang disinkronkan ke session server saat halaman keranjang dibuka; endpoint server tetap tersedia sebagai fallback ketika JavaScript tidak aktif.
3. **Checkout Transaksi**: `WebCheckoutController` meneruskan proses ke mekanisme bersama `MobileCheckoutController`. Server memvalidasi `checkout_token`, mengunci request duplikat, dan memproses pesanan dalam `DB::transaction()`.
4. **Reservasi Stok**: Semua produk dikunci menggunakan `lockForUpdate()` dalam urutan ID yang konsisten. Stok diperiksa ulang dan dikurangi sebelum order dibuat dengan `stock_reserved = true`.

### B. Alur Manajemen Admin
1. **Otentikasi & Keamanan**: Admin login diverifikasi terhadap hash password dengan batasan percobaan gagal (maks. 5 kali sebelum lockout 15 menit).
2. **Konfirmasi Pesanan**: `Admin\OrderController@confirm` mengunci record order dan mencatat `Transaction`. Stok hanya dikurangi untuk pesanan lama dengan `stock_reserved = false`; pesanan baru tidak dikurangi dua kali.
3. **Penjualan Kasir Manual**: `Admin\TransactionController@manualSale` mencatat penjualan langsung dan memotong stok seketika dalam satu transaksi atomik.

---

## 4. Keamanan & Performa

1. **Proteksi Akses**: Menggunakan middleware session admin khusus (`admin.session`) dan pembatasan throttle pada rute login (`throttle:10,1`).
2. **Integritas Konkurensi Stok**: Menggunakan *pessimistic locking* (`lockForUpdate()`) di dalam database transaction, urutan lock produk yang konsisten, dan flag `orders.stock_reserved` agar tidak terjadi *overselling* atau pengurangan stok ganda.
3. **Idempotensi Checkout**: Setiap form checkout memiliki token UUID. Cache lock mengoordinasikan request dengan token yang sama dan token yang telah dipakai tidak dapat membuat order kedua.
4. **CSRF & XSS Protection**: Seluruh form web dilindungi token CSRF otomatis (`@csrf`) dan Blade escaping.
