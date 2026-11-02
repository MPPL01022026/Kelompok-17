# 🗄️ Skema Basis Data (Database Schema) Sistem Ayamo — v2.1

Dokumen ini adalah revisi skema basis data Ayamo untuk alur baru: **pelanggan tanpa login**, pembayaran **Tunai / Transfer (Bank & QRIS statis)** yang diverifikasi lewat WhatsApp, dan **aplikasi admin terpisah** (lihat penjualan + CRUD menu) yang memakai database yang sama.

> **Pembaruan 2 November 2026:** skema pesanan memiliki penanda `stock_reserved` untuk membedakan pesanan yang stoknya sudah dialokasikan secara atomik dengan pesanan lama yang belum memakai mekanisme reservasi. Kolom ini sudah tersedia melalui migration `2026_11_02_052334_add_stock_reserved_to_orders_table.php`.

### Status implementasi skema

Dokumen ini memuat **skema target v2.1** sekaligus persyaratan integritas yang wajib dipertahankan. Implementasi aktif saat pembaruan ini masih berada pada tahap transisi:

| Area | Implementasi aktif | Target v2.1 |
| :--- | :--- | :--- |
| Detail item pesanan | `orders.items` dalam JSON | Tabel ter-normalisasi `order_items` |
| Rekap penjualan | Tabel `transactions` masih digunakan | Status pembayaran terintegrasi pada `orders` |
| Reservasi stok | `orders.stock_reserved` melalui migration baru | Tetap dipertahankan sebagai penanda alokasi stok |

Bagian target yang belum memiliki migration tidak boleh dianggap sudah tersedia di database deployment.

---

## 1. Diagram Relasi Entitas (ERD)

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "memverifikasi pembayaran"
    ORDERS ||--|{ ORDER_ITEMS : "berisi"
    PRODUCTS ||--o{ ORDER_ITEMS : "dipesan sebagai"

    PRODUCTS {
        string id PK "Slug unik (e.g. paha-atas)"
        string name "Nama Produk"
        string group_name "Grup tampilan (e.g. Paha Ayam), nullable"
        string category "Ayam / Camilan"
        unsigned_int price "Harga Jual (Rp)"
        unsigned_int stock "Stok, NULL = tidak dilacak"
        text description "Deskripsi"
        string image_url "Path / URL Foto"
        boolean is_available "Habis sementara / tersedia"
        boolean active "Tampil di etalase / diarsipkan"
        smallint sort_order "Urutan tampil"
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        char id PK "UUID v4"
        string code UK "Kode tampil (e.g. AYM-0123)"
        string tracking_token UK "Token acak untuk link status"
        string customer_name "Nama Pemesan"
        string phone "WhatsApp (format 62xxx)"
        text note "Catatan pelanggan"
        string source "online / manual"
        unsigned_int subtotal "Jumlah harga item (Rp)"
        smallint unique_code "Kode unik transfer (0 untuk tunai)"
        unsigned_int total "subtotal + unique_code"
        string payment_method "cash / transfer"
        string payment_channel "bank / qris (NULL jika tunai)"
        string payment_status "unpaid / paid"
        timestamp paid_at "Waktu pembayaran terverifikasi"
        bigint verified_by FK "users.id admin pemverifikasi"
        string status "pending / processing / ready / completed / cancelled"
        boolean stock_reserved "Stok sudah dialokasikan, default false"
        string cancel_reason "Alasan batal"
        timestamp expires_at "Batas waktu bayar transfer"
        timestamp created_at
        timestamp updated_at
    }

    ORDER_ITEMS {
        bigint id PK
        char order_id FK "orders.id"
        string product_id FK "products.id (nullable)"
        string product_name "Snapshot nama"
        unsigned_int unit_price "Snapshot harga satuan"
        smallint quantity "Jumlah"
        unsigned_int subtotal "unit_price x quantity"
    }

    STORE_SETTINGS {
        string key PK "e.g. bank_account_number"
        text value "Nilai pengaturan"
        timestamp updated_at
    }

    LOGIN_ATTEMPTS {
        string identifier PK "Username / IP admin"
        unsigned_int attempts "Jumlah gagal"
        timestamp locked_until "Lockout"
        timestamp created_at
        timestamp updated_at
    }

    USERS {
        bigint id PK
        string name "Nama Admin"
        string email "Email"
        timestamp email_verified_at
        string password "Hashed Password"
        string role "owner / staff"
        string remember_token
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Rincian Skema Tabel

### A. Tabel `products`
Katalog menu. Menu awal ada 6 item; **Paha Ayam dipisah jadi 2 baris** (Atas & Bawah) supaya stok dan harga masing-masing bisa diatur sendiri.

| Kolom | Tipe Data | Nullable | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `VARCHAR(100)` | No | Primary Key (slug, misal `paha-atas`) |
| `name` | `VARCHAR(255)` | No | Nama item (misal *Paha Atas*) |
| `group_name` | `VARCHAR(255)` | Yes | Untuk menggabungkan varian di UI (misal *Paha Ayam*) |
| `category` | `VARCHAR(50)` | No | `Ayam` atau `Camilan` |
| `price` | `INT UNSIGNED` | No | Harga dalam Rupiah |
| `stock` | `INT UNSIGNED` | Yes | Sisa stok; `NULL` = tidak dilacak |
| `description` | `TEXT` | Yes | Deskripsi rasa/isi |
| `image_url` | `VARCHAR(255)` | Yes | Path foto |
| `is_available` | `BOOLEAN` | No | `false` = tampil tapi tombol pesan nonaktif (habis sementara), default `true` |
| `active` | `BOOLEAN` | No | `false` = diarsipkan (tidak tampil), default `true` |
| `sort_order` | `SMALLINT` | No | Urutan tampil, default `0` |
| `created_at` / `updated_at` | `TIMESTAMP` | Yes | Audit waktu |

*Indeks:* `(active, category, sort_order)` untuk query etalase.

**Seed menu awal**

| id | name | group_name | category | price |
| :--- | :--- | :--- | :--- | :--- |
| `dada` | Dada Ayam | — | Ayam | 18000 |
| `sayap` | Sayap Ayam | — | Ayam | *(isi)* |
| `paha-atas` | Paha Atas | Paha Ayam | Ayam | 16000 |
| `paha-bawah` | Paha Bawah | Paha Ayam | Ayam | *(isi)* |
| `ceker` | Ceker Ayam | — | Ayam | *(isi)* |
| `hati` | Hati Ayam | — | Ayam | *(isi)* |
| `bakso-goreng-stick` | Bakso Goreng Stick | — | Camilan | 12000 |

---

### B. Tabel `orders`
Satu baris per pesanan dari pelanggan (online) atau diinput admin (manual/walk-in). **Status pesanan dan status pembayaran dipisah** karena siklusnya beda.

| Kolom | Tipe Data | Nullable | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `CHAR(36)` | No | Primary Key (UUID v4) |
| `code` | `VARCHAR(20)` | No | **Unique.** Kode pendek untuk kasir/WA (misal `AYM-0123`) |
| `tracking_token` | `VARCHAR(32)` | No | **Unique.** Token acak untuk link status pelanggan |
| `customer_name` | `VARCHAR(255)` | No | Nama pemesan |
| `phone` | `VARCHAR(20)` | No | WhatsApp, disimpan format `62xxxxxxxxxx` |
| `note` | `TEXT` | Yes | Catatan pelanggan |
| `source` | `VARCHAR(20)` | No | `online` / `manual`, default `online` |
| `subtotal` | `INT UNSIGNED` | No | Total harga item |
| `unique_code` | `SMALLINT UNSIGNED` | No | Kode unik transfer 1–999; `0` untuk tunai |
| `total` | `INT UNSIGNED` | No | `subtotal + unique_code`, nominal yang harus dibayar |
| `payment_method` | `VARCHAR(20)` | No | `cash` / `transfer` |
| `payment_channel` | `VARCHAR(20)` | Yes | `bank` / `qris`; `NULL` jika tunai |
| `payment_status` | `VARCHAR(20)` | No | `unpaid` / `paid`, default `unpaid` |
| `paid_at` | `TIMESTAMP` | Yes | Saat admin memverifikasi / kasir menerima uang |
| `verified_by` | `BIGINT UNSIGNED` | Yes | FK `users.id` (admin yang memverifikasi) |
| `status` | `VARCHAR(20)` | No | `pending` → `processing` → `ready` → `completed`, atau `cancelled` |
| `stock_reserved` | `BOOLEAN` | No | `true` jika seluruh stok item sudah dialokasikan; default `false` untuk kompatibilitas pesanan lama |
| `cancel_reason` | `VARCHAR(255)` | Yes | Alasan pembatalan |
| `expires_at` | `TIMESTAMP` | Yes | Batas bayar untuk transfer; lewat batas → otomatis `cancelled` |
| `created_at` / `updated_at` | `TIMESTAMP` | Yes | Audit waktu |

*Indeks:* `(status, created_at)` untuk antrean dashboard, `(payment_status, paid_at)` untuk laporan penjualan, `(phone)` untuk pencarian.

**Alur status**

```mermaid
stateDiagram-v2
    [*] --> pending
    pending --> processing : pembayaran terverifikasi / tunai diterima admin
    processing --> ready : pesanan siap diambil
    ready --> completed : diambil pelanggan
    pending --> cancelled : ditolak / kedaluwarsa
    processing --> cancelled : dibatalkan admin
```

---

### C. Tabel `order_items`
Menggantikan kolom JSON `items` pada skema lama, supaya laporan "menu terlaris" cukup satu query `GROUP BY`.

| Kolom | Tipe Data | Nullable | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Primary Key (auto increment) |
| `order_id` | `CHAR(36)` | No | FK `orders.id`, `ON DELETE CASCADE` |
| `product_id` | `VARCHAR(100)` | Yes | FK `products.id`, `ON DELETE SET NULL` |
| `product_name` | `VARCHAR(255)` | No | **Snapshot** nama saat dipesan |
| `unit_price` | `INT UNSIGNED` | No | **Snapshot** harga satuan saat dipesan |
| `quantity` | `SMALLINT UNSIGNED` | No | Jumlah |
| `subtotal` | `INT UNSIGNED` | No | `unit_price × quantity` |

*Indeks:* `(order_id)`, `(product_id)`.

---

### D. Tabel `store_settings`
Pengaturan toko yang diedit dari aplikasi admin (key–value), supaya nomor WA, rekening, dan QRIS tidak di-hardcode di aplikasi pelanggan.

| Kolom | Tipe Data | Nullable | Keterangan |
| :--- | :--- | :--- | :--- |
| `key` | `VARCHAR(100)` | No | Primary Key |
| `value` | `TEXT` | Yes | Nilai |
| `updated_at` | `TIMESTAMP` | Yes | Waktu diubah |

Key yang dipakai: `store_open`, `open_hours`, `address`, `whatsapp_number`, `bank_name`, `bank_account_number`, `bank_account_name`, `qris_image_url`, `order_expiry_minutes`.

---

### E. Tabel `users` dan `login_attempts`
**Hanya untuk admin.** Pelanggan tidak punya akun. `users` menambah kolom `role` (`owner` / `staff`); `login_attempts` tetap sebagai anti *brute-force*, dan `identifier` sekarang berisi username/IP admin.

---

## 3. Perubahan dari Skema Lama

| Skema lama | Skema baru | Alasan |
| :--- | :--- | :--- |
| `orders.items` (JSON) | Tabel `order_items` | Laporan per menu tanpa parsing JSON; snapshot harga tetap aman |
| Tabel `transactions` | Dihapus; penjualan = `orders` dengan `payment_status = 'paid'`, jual manual = `source = 'manual'` | Menghilangkan data ganda antara `orders` dan `transactions` |
| `status`: pending/confirmed/cancelled | `status` + `payment_status` terpisah | Alur baru punya tahap diproses & siap diambil; bayar dan masak itu dua hal berbeda |
| `payment_method`: cod/whatsapp/qris | `payment_method` (cash/transfer) + `payment_channel` (bank/qris) | Sesuai desain: Transfer punya sub-pilihan Bank atau QRIS |
| — | `code`, `tracking_token`, `unique_code`, `expires_at` | Lacak pesanan tanpa login, cocokkan transfer, lepas stok pesanan kedaluwarsa |
| — | `orders.stock_reserved` | Mencegah stok dikurangi dua kali dan menandai pesanan lama yang masih perlu alokasi stok saat dikonfirmasi |
| — | `store_settings` | Rekening/QRIS/WA bisa diubah admin |
| `products.description`, `image_url` NOT NULL | Nullable | Menu baru bisa disimpan dulu sebelum fotonya ada |
| `login_attempts` untuk "Username / IP Pelanggan" | Khusus admin | Pelanggan tidak login |

---

## 4. Catatan Implementasi

1. **Kode order jangan dipakai sebagai satu-satunya kunci akses.** Kode 4 digit mudah ditebak, dan di halaman status ada nama dan nomor WA. Link status pelanggan pakai `tracking_token` (acak), atau minta 4 digit terakhir nomor WA untuk verifikasi.
2. **Stok direservasi saat order dibuat, di dalam DB transaction.** Baris produk dikunci dengan `SELECT ... FOR UPDATE`, diperiksa ulang, lalu stok dikurangi dan `orders.stock_reserved` diubah menjadi `true` dalam transaksi yang sama. Tanpa ini, dua pelanggan bisa memesan stok terakhir secara bersamaan.
3. **Total transfer = `subtotal + unique_code`.** Admin mencocokkan mutasi bank/QRIS dengan nominal unik ini, bukan dengan nama.
4. **Riwayat di aplikasi pelanggan** disimpan di perangkat (localStorage) dan dicocokkan ke server lewat `tracking_token`. Ganti HP berarti riwayat hilang; itu konsekuensi desain tanpa login.
5. **Laporan penjualan:** omzet per hari = `SUM(total)` dari `orders` dengan `payment_status = 'paid'` dikelompokkan per `DATE(paid_at)`. Menu terlaris = `SUM(quantity)` dari `order_items` yang join ke order berstatus `paid`.

---

## 5. Persyaratan Integritas Stok dan Race Condition

Mekanisme berikut wajib berlaku untuk checkout **mobile dan web**:

1. Keranjang di sisi pelanggan boleh disimpan secara lokal dan baru disinkronkan ketika pelanggan membuka keranjang atau checkout. Nilai harga, status produk, dan stok dari browser **tidak boleh dipercaya** sebagai sumber final.
2. Setiap checkout membawa `checkout_token` unik. Token yang sama tidak boleh menghasilkan lebih dari satu pesanan, termasuk saat tombol checkout ditekan berulang atau dua request tiba hampir bersamaan.
3. Proses checkout wajib memakai database transaction dan `lockForUpdate()` pada seluruh produk yang dipesan. Produk dikunci dalam urutan ID yang konsisten untuk mengurangi risiko deadlock.
4. Setelah lock diperoleh, server wajib membaca ulang produk aktif, harga, dan stok. Pesanan ditolak apabila satu produk hilang, nonaktif, atau stoknya kurang.
5. Pengurangan `products.stock`, pembuatan `orders`, dan perubahan `orders.stock_reserved = true` harus berhasil atau gagal sebagai satu unit atomik.
6. Konfirmasi admin hanya mengurangi stok apabila `stock_reserved = false`. Pesanan baru yang sudah mereservasi stok saat checkout tidak boleh mengurangi stok untuk kedua kalinya.
7. Pesanan lama memiliki nilai default `stock_reserved = false`. Saat dikonfirmasi, sistem menjalankan alokasi stok dengan lock dan kemudian mengubah nilainya menjadi `true`.
8. Saat alur pembatalan atau kedaluwarsa diterapkan, pelepasan stok hanya dilakukan untuk pesanan dengan `stock_reserved = true`, kemudian flag dikembalikan ke `false` dalam transaction yang sama.

### Migration

```text
database/migrations/2026_11_02_052334_add_stock_reserved_to_orders_table.php
```

Migration menambahkan kolom berikut:

```sql
stock_reserved BOOLEAN NOT NULL DEFAULT FALSE
```

Migration wajib dijalankan pada setiap environment sebelum versi aplikasi dengan mekanisme reservasi stok dipublikasikan:

```bash
php artisan migrate
```

## 6. Eloquent Model Mapping

| Tabel | Eloquent Model |
| :--- | :--- |
| `products` | `App\Models\Product` |
| `orders` | `App\Models\Order` |
| `order_items` | `App\Models\OrderItem` |
| `store_settings` | `App\Models\StoreSetting` |
| `login_attempts` | `App\Models\LoginAttempt` |
| `users` | `App\Models\User` |
