# Ayamo — Sistem Informasi & Statistik Penjualan UMKM

Sistem berbasis web untuk pengelolaan katalog produk, pemesanan, dan pencatatan penjualan UMKM **Ayamo** (fried chicken). Aplikasi ini menggantikan proses pencatatan konvensional agar pelanggan dapat melihat ketersediaan produk secara daring dan pemilik usaha memperoleh rekap serta visualisasi tren penjualan yang akurat.

Proyek ini disusun sebagai pemenuhan tugas mata kuliah Manajemen Proyek Perangkat Lunak.

---

## 1. Kelompok 17

| Nama                         | Peran dalam Tim                                |
| :--------------------------- | :--------------------------------------------- |
| Ruhmita                      | Manajer Proyek                                 |
| Muhammad Rifki Aulia Pratama | Pengembang                                     |
| Rafi Alamsyah                | Pengembang sekaligus Sponsor / _Owner_ UMKM    |
| Fazah Rizki Fatahillah       | Pengembang                                     |

Seluruh anggota berperan sebagai pengembang. Rafi Alamsyah berperan ganda sebagai pengembang dan pemilik UMKM, karena usahanya menjadi objek studi kasus.

---

## 2. Ikhtisar Proyek

**Nama Proyek:** Pembangunan Sistem Informasi dan Statistik Penjualan UMKM Ayamo

**Latar Belakang:** UMKM Ayamo masih memasarkan produk dan mencatat penjualan secara konvensional. Kondisi ini menyulitkan pelanggan dalam melihat ketersediaan stok secara daring dan menyulitkan pemilik dalam memantau tren perkembangan usaha. Diperlukan sistem berbasis web yang menampilkan katalog produk sekaligus menyediakan rekap dan statistik penjualan.

**Dasar Pemilihan Studi Kasus:** Usaha Ayamo merupakan bisnis milik Rafi Alamsyah, salah satu anggota kelompok yang juga berperan sebagai pengembang. Studi kasus ini diangkat agar sistem yang dibangun berlandaskan permasalahan nyata, sehingga proses analisis kebutuhan, validasi fitur, dan evaluasi dapat dilakukan langsung bersama pemilik usaha yang sekaligus terlibat dalam pengembangan.

**Tujuan Proyek:**

1. Membangun situs interaktif untuk menampilkan katalog produk (jenis, harga, deskripsi, dan stok).
2. Menyediakan _dashboard_ admin dengan riwayat serta grafik statistik penjualan sebagai penunjang analisis bisnis pemilik UMKM.
3. Mempermudah pencatatan transaksi secara digital agar lebih terstruktur.

**Ruang Lingkup:**

- _In-Scope:_ halaman beranda dan katalog produk, fitur keranjang/pemesanan sederhana, _dashboard_ admin untuk manajemen produk (tambah, edit, hapus), serta grafik statistik penjualan.
- _Out-of-Scope:_ integrasi _payment gateway_ otomatis dan pelacakan kurir berbasis GPS secara _real-time_.

---

## 3. Project Charter — Jadwal Kasar

| Pekan | Kegiatan                                                                                            |
| :---- | :-------------------------------------------------------------------------------------------------- |
| 1     | Analisis kebutuhan, perancangan desain (wireframe dan basis data), serta penyusunan dokumen proyek. |
| 2     | Pembuatan basis data dan pengembangan tampilan utama (katalog & frontend).                          |
| 3     | Pembuatan dashboard admin, integrasi grafik statistik penjualan, dan pengujian sistem.              |
| 4     | Evaluasi bersama pemilik UMKM, perbaikan akhir, dan penyerahan hasil.                               |

---

## 4. Stakeholder Register

| ID  | Stakeholder            | Peran                | Kategori            | Interest | Influence |
| :-- | :--------------------- | :------------------- | :------------------ | :------- | :-------- |
| S01 | Rafi Alamsyah          | Owner & Developer    | Internal / Business | High     | High      |
| S02 | Ruhmita                | Project Manager UMKM | Internal            | High     | High      |
| S03 | Muhammad Rifki         | Developer            | Internal            | High     | Medium    |
| S04 | Fazah Rizki Fatahillah | Developer            | Internal            | High     | Medium    |
| S05 | Pelanggan Ayamo        | End User             | External            | Medium   | Medium    |
| S06 | Ibu Cut Alna Fadila    | Dosen / Supervisor   | External / Academic | High     | High      |

---

## 5. Work Breakdown Structure (WBS)

```text
1.0 Manajemen & Perencanaan Proyek
    1.1 Wawancara pemilik UMKM (jenis produk & parameter statistik)
    1.2 Penyusunan Project Charter, Stakeholder Register, WBS, dan Workspace
2.0 Perancangan Sistem & Desain
    2.1 Pembuatan wireframe/mockup halaman katalog dan grafik (Figma)
    2.2 Perancangan struktur basis data
3.0 Pengembangan & Pembuatan Website
    3.1 Pengembangan basis data & koneksi
    3.2 Pengembangan frontend katalog (beranda, daftar produk, detail produk)
    3.3 Pengembangan dashboard admin & statistik (CRUD produk, pencatatan
        transaksi, integrasi pustaka grafik)
4.0 Pengujian Sistem
    4.1 Uji fungsi katalog dan alur pemesanan
    4.2 Validasi keakuratan data grafik bersama pemilik UMKM
    4.3 Perbaikan bug/kesalahan program
5.0 Penyelesaian & Dokumentasi
    5.1 Penyusunan laporan akhir
    5.2 Presentasi dan demonstrasi sistem
```

**Estimasi Function Point (FP):** Count Total 79, ΣFi 25, _Adjustment Factor_ 0,90 — menghasilkan ukuran fungsional sebesar **71,1 FP** (dibulatkan 71 FP), dengan rincian EI 6, EO 3, EQ 3, ILF 4, dan EIF 0.

---

## 6. Tumpukan Teknologi (_Tech Stack_)

| Lapisan            | Teknologi                                                    |
| :----------------- | :----------------------------------------------------------- |
| Bahasa & Framework | PHP 8.2+, Laravel 12                                         |
| Komponen Reaktif   | Livewire 3, Livewire Volt (single-file component), Alpine.js |
| Antarmuka          | Livewire Flux UI, Tailwind CSS v4                            |
| Build Tool         | Vite 6                                                       |
| Basis Data         | MySQL / MariaDB (dukungan bawaan SQLite)                     |
| Pengujian          | Pest PHP                                                     |
| Kualitas Kode      | Laravel Pint                                                 |

### Struktur Repositori

```text
Kelompok-17/
├── app/                # Aplikasi Laravel 12 (kode sumber)
├── docs/SURVEY/        # Dokumen manajemen proyek (charter, WBS, stakeholder)
└── README.md
```

---

## 7. Struktur Aplikasi & Instalasi Singkat

**Struktur direktori utama (`app/`):**

```text
app/
├── app/
│   ├── Http/Controllers/     # Controller (termasuk autentikasi)
│   ├── Livewire/Actions/     # Aksi Livewire (mis. Logout)
│   └── Models/               # Model Eloquent
├── database/
│   ├── factories/            # Factory untuk pengujian
│   ├── migrations/           # Skema tabel
│   └── seeders/              # Data awal
├── resources/views/          # Blade, layout, komponen Flux, Livewire
└── routes/                   # Routing web & auth
```

**Menjalankan aplikasi:**

```bash
cd app
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

`composer run dev` menjalankan server PHP, queue listener, log viewer, dan Vite secara paralel.

### Status Pengembangan

Repositori `Kelompok-17` merupakan versi **primer** dan aktif. Fondasi aplikasi (Laravel 12, Livewire/Volt, Flux UI, autentikasi) telah tersedia, sementara modul POS/kasir, manajemen menu, inventaris, dan laporan analitik dikembangkan mengikuti [roadmap](docs/) bertahap. Proyek `anu` dan `ayamo` merupakan artefak terdahulu dan dokumentasi lama yang bersifat arsip.
