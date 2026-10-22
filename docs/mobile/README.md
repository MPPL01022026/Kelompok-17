<div align="center">

# 📱 Dokumentasi Mobile View — Ayamo
### Panduan Implementasi & Referensi Pengembangan Mobile

[![Figma Mockup](https://img.shields.io/badge/Lihat%20Mockup-Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white)](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind](https://img.shields.io/badge/TailwindCSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

</div>

---

## 📋 Tentang Direktori Ini

Direktori `docs/mobile/` berisi seluruh dokumentasi teknis terkait **implementasi Mobile View** dari aplikasi web Ayamo. Mobile View adalah tampilan responsif dari aplikasi web Laravel yang dioptimalkan untuk pengguna smartphone.

> 💡 **Mobile View Ayamo** bukan aplikasi native (Android/iOS), melainkan halaman web yang sepenuhnya responsif dan dioptimalkan untuk perangkat seluler menggunakan **Tailwind CSS v4** breakpoints dan komponen **Livewire Volt**.

---

## 🎯 Cakupan Mobile View

Mobile View Ayamo mencakup dua jenis pengguna utama:

| Pengguna     | Akses                   | Fitur Utama                                               |
| ------------ | ----------------------- | --------------------------------------------------------- |
| **Pelanggan**| Tampilan menu & pesanan | Beranda, katalog menu, keranjang, pembayaran, riwayat     |
| **Kasir**    | Panel kasir mobile      | POS mobile-friendly, konfirmasi pesanan, struk digital    |

---

## 📐 Spesifikasi Teknis Mobile

| Aspek               | Spesifikasi                                                          |
| ------------------- | -------------------------------------------------------------------- |
| **Breakpoint**      | `sm: 640px` ke bawah dianggap tampilan mobile utama                  |
| **Framework CSS**   | Tailwind CSS v4 (utility-first, mobile-first approach)               |
| **Komponen UI**     | Livewire Volt (single-file components) + Livewire Flux               |
| **Navigasi**        | Bottom Navigation Bar (3 tab: Beranda, Menu, Riwayat)                |
| **Interaktivitas**  | Alpine.js untuk micro-interactions (modal, dropdown, cart toggle)    |
| **Performa**        | Livewire lazy loading untuk item menu & riwayat transaksi panjang    |

---

## 🗂️ Struktur Halaman Mobile

```
Mobile View Ayamo
├── Onboarding
│   ├── Splash Screen (Tampilan Awal)
│   └── Inisialisasi Aplikasi
│
├── Utama (Bottom Nav)
│   ├── 🏠 Beranda
│   │   ├── Hero Banner Promo
│   │   ├── Kategori Cepat (Dada, Paha, Ceker, Sayap, Leher, Bakso)
│   │   ├── Menu Favorit (Grid produk)
│   │   └── Info Toko
│   │
│   ├── 📋 Menu (Katalog Lengkap)
│   │   ├── Filter Kategori
│   │   └── Daftar Produk + Tambah ke Keranjang
│   │
│   └── 📜 Riwayat Transaksi
│
├── Alur Transaksi
│   ├── 🛒 Keranjang
│   │   ├── Metode: Tunai
│   │   └── Metode: Transfer Bank
│   │
│   ├── 💳 Pembayaran
│   │   ├── Tunai (input + hitung kembalian)
│   │   ├── Transfer Bank (no. rekening)
│   │   └── QRIS (QR Code scan)
│   │
│   └── 🧾 Detail Transaksi / Struk Digital
│       ├── Tunai
│       ├── QRIS
│       └── Bank Transfer
│
└── Support
    ├── Bantuan — Kendala
    └── Bantuan — Konfirmasi Pesanan
```

---

## 🎨 Referensi Desain

### Link Figma (Mockup Mobile)

> 🔗 **[Buka di Figma →](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1)**

```
https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1
```

### Screenshot Mockup

Seluruh aset mockup (17 screen) tersedia di:

📁 [`../mockup/mobile/`](../mockup/mobile/) — Folder screenshot PNG semua layar mobile
📄 [`../mockup/mobile/README.md`](../mockup/mobile/README.md) — Dokumentasi lengkap setiap screen

### Design Tokens

| Token             | Nilai                                                         |
| ----------------- | ------------------------------------------------------------- |
| `--color-primary` | `#F97316` (orange-500) — CTA, tombol utama, aksen brand       |
| `--color-accent`  | `#EF4444` (red-500) — Badge, notifikasi, hapus                |
| `--color-surface` | `#FFF7ED` (orange-50) — Background halaman utama              |
| `--color-text`    | `#1C1917` (stone-900) — Teks utama                            |
| `--radius-card`   | `16px` — Border radius kartu produk                           |
| `--radius-btn`    | `999px` — Border radius tombol (pill shape)                   |

---

## 🛠️ Panduan Implementasi

### Struktur Komponen Volt (Mobile)

```bash
resources/views/livewire/
├── mobile/
│   ├── home.blade.php          # Beranda mobile
│   ├── menu-catalog.blade.php  # Katalog menu mobile
│   ├── cart.blade.php          # Keranjang & checkout
│   ├── payment/
│   │   ├── cash.blade.php      # Pembayaran tunai
│   │   ├── bank-transfer.blade.php
│   │   └── qris.blade.php
│   ├── transaction-detail.blade.php
│   ├── history.blade.php
│   └── help.blade.php
```

### Konvensi Responsif Tailwind

```html
<!-- Gunakan mobile-first: default = mobile, md: = desktop -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <!-- 2 kolom di mobile, 4 kolom di desktop -->
</div>

<!-- Bottom Navigation hanya tampil di mobile -->
<nav class="fixed bottom-0 md:hidden w-full bg-white border-t">
    <!-- Tab: Beranda, Menu, Riwayat -->
</nav>
```

### Komponen Bottom Navigation

```html
<!-- resources/views/components/mobile/bottom-nav.blade.php -->
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-orange-100 md:hidden">
    <div class="flex items-center justify-around py-2">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 text-orange-500">
            <x-icon name="home" class="w-6 h-6" />
            <span class="text-xs font-medium">Beranda</span>
        </a>
        <a href="{{ route('menu') }}" class="flex flex-col items-center gap-1 text-stone-400">
            <x-icon name="utensils" class="w-6 h-6" />
            <span class="text-xs font-medium">Menu</span>
        </a>
        <a href="{{ route('history') }}" class="flex flex-col items-center gap-1 text-stone-400">
            <x-icon name="clock" class="w-6 h-6" />
            <span class="text-xs font-medium">Riwayat</span>
        </a>
    </div>
</nav>
```

---

## 📊 Status Implementasi

| Halaman                    | Mockup | Implementasi | Testing |
| -------------------------- | :----: | :----------: | :-----: |
| Splash Screen              |   ✅   |      🔄      |   ⏳    |
| Beranda                    |   ✅   |      🔄      |   ⏳    |
| Katalog Menu               |   ✅   |      🔄      |   ⏳    |
| Keranjang (Tunai)          |   ✅   |      🔄      |   ⏳    |
| Keranjang (Transfer)       |   ✅   |      🔄      |   ⏳    |
| Pembayaran — Tunai         |   ✅   |      🔄      |   ⏳    |
| Pembayaran — Bank          |   ✅   |      🔄      |   ⏳    |
| Pembayaran — QRIS          |   ✅   |      🔄      |   ⏳    |
| Detail Transaksi           |   ✅   |      🔄      |   ⏳    |
| Riwayat                    |   ✅   |      🔄      |   ⏳    |
| Bantuan                    |   ✅   |      🔄      |   ⏳    |

**Keterangan:** ✅ Selesai · 🔄 Sedang Berjalan · ⏳ Belum Dimulai

---

## 🔗 Navigasi Dokumen

| Dokumen                          | Link                                                    |
| -------------------------------- | ------------------------------------------------------- |
| 📸 **Mockup Mobile (Screenshot)**| [`../mockup/mobile/README.md`](../mockup/mobile/README.md) |
| 🎨 **Figma Design**              | [Buka di Figma](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1) |
| 🏗️ **Arsitektur Sistem**         | [`../requirements/architecture.md`](../requirements/architecture.md) |
| 📋 **Spesifikasi Fitur**         | [`../requirements/features-and-modules.md`](../requirements/features-and-modules.md) |
| 🏠 **README Utama Proyek**       | [`../../README.md`](../../README.md)                    |

---

<div align="center">

**🍗 AYAMO — EAT • CHILL • REPEAT**
Dokumentasi Mobile View · Tugas MPPL · 2026

*Laravel 12 + Livewire Volt + Tailwind CSS v4*

</div>

