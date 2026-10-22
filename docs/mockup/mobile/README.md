<div align="center">

# 📱 Mockup UI/UX — Mobile View
### Ayamo Management System · *Eat. Chill. Repeat.*

[![Figma](https://img.shields.io/badge/Buka%20di%20Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white)](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1)
[![Platform](https://img.shields.io/badge/Platform-Mobile%20Web%20Responsive-EA580C?style=for-the-badge&logo=googlechrome&logoColor=white)](#)
[![Status](https://img.shields.io/badge/Status-Selesai%20Dirancang-22C55E?style=for-the-badge&logo=checkmarx&logoColor=white)](#)

**🔗 Link Figma:**
[https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1)

</div>

---

## 📋 Tentang Mockup Mobile Ini

Mockup ini merepresentasikan **tampilan antarmuka mobile-responsive** dari aplikasi web Ayamo yang dioptimalkan untuk perangkat seluler (smartphone). Seluruh desain dirancang menggunakan **Figma** dan diimplementasikan ke dalam antarmuka web berbasis **Laravel + Livewire Volt + Tailwind CSS v4**.

> ⚠️ **Catatan Penting:** Ini adalah **Mobile Web View** (bukan native Android/iOS app), melainkan tampilan responsif dari aplikasi web Laravel yang diakses melalui peramban di perangkat seluler.

---

## 🎨 Design System

| Elemen            | Nilai / Keterangan                                        |
| ----------------- | --------------------------------------------------------- |
| **Warna Primer**  | `#F97316` — Orange (brand Ayamo)                          |
| **Warna Aksen**   | `#EF4444` — Merah untuk aksi & notifikasi                 |
| **Warna Dasar**   | `#FFF7ED` — Krem muda (warm background)                   |
| **Tipografi**     | Sans-serif modern, bold heading                           |
| **Border Radius** | Rounded corners (12px–20px)                               |
| **Navigasi**      | Bottom Navigation Bar (3 tab: Beranda, Menu, Riwayat)     |

---

## 📱 Inventaris Screen & Alur Pengguna

### 🔁 Alur Lengkap Aplikasi

```
[Ikon App] → [Splash Screen] → [Beranda] → [Menu]
                                              ↓
                                        [Keranjang] → [Metode Pembayaran]
                                              ↓               ↓
                                        [Tunai / Transfer / QRIS]
                                              ↓
                                   [Detail Transaksi] → [Riwayat]
                                              ↓
                                    [Bantuan / Kendala]
```

---

### 1. 📲 Ikon Aplikasi di Launcher

**File:** [`Android Apps.png`](./Android%20Apps.png)

Tampilan ikon aplikasi Ayamo pada layar *home screen* perangkat Android. Ikon berbentuk logo maskot ayam Ayamo dengan background gelap khas launcher Android.

---

### 2. 🚀 Splash Screen — Tampilan Awal

**File:** [`Tampilan Awal.png`](./Tampilan%20Awal.png)

Layar pembuka (splash screen) yang muncul saat aplikasi pertama kali dibuka. Menampilkan logo Ayamo dengan gradient warna brand orange–kuning yang hangat.

| Elemen         | Deskripsi                                    |
| -------------- | -------------------------------------------- |
| **Background** | Gradient orange → kuning (brand Ayamo)       |
| **Logo**       | Logo maskot ayam + wordmark "AYAMO"          |
| **Tagline**    | *"EAT. CHILL. REPEAT."*                      |
| **Durasi**     | Tampil sementara sebelum redirect ke beranda |

---

### 3. 🏠 Beranda (Home)

**File:** [`beranda mobile.png`](./beranda%20mobile.png)

Halaman utama yang ditampilkan setelah splash screen. Berisi navigasi kategori menu, produk favorit, dan info toko. Ini adalah titik masuk utama pengguna untuk menjelajahi menu.

| Komponen           | Deskripsi                                                  |
| ------------------ | ---------------------------------------------------------- |
| **Header Logo**    | Logo Ayamo + tagline                                       |
| **Search Bar**     | Pencarian menu cepat ("Cari dada, paha, sayap...")         |
| **Hero Banner**    | Promo "Ayam Crispy Favorit" dengan tombol CTA              |
| **Kategori Cepat** | Shortcut kategori: Dada, Paha, Ceker, Sayap, Leher, Bakso |
| **Menu Favorit**   | Grid produk dengan harga dan tombol "Tambah"               |
| **Info Toko**      | Alamat & jam buka Ayamo                                    |
| **Floating Cart**  | Bar keranjang aktif (item + total harga)                   |
| **Bottom Nav**     | Beranda · Menu · Riwayat                                   |

---

### 4. 📋 Halaman Menu

**File:** [`Menu mobile.png`](./Menu%20mobile.png)

Halaman katalog menu lengkap dengan semua item produk yang tersedia, dikelompokkan berdasarkan kategori.

---

### 5. 🛒 Keranjang — Metode Tunai

**File:** [`Keranjang - metode tunai.png`](./Keranjang%20-%20metode%20tunai.png)

Tampilan halaman keranjang belanja ketika pelanggan memilih metode pembayaran **Tunai**. Menampilkan ringkasan pesanan dan total yang harus dibayar.

---

### 6. 🏧 Keranjang — Metode Transfer

**File:** [`Keranjang - transfer.png`](./Keranjang%20-%20transfer.png)

Tampilan keranjang belanja untuk metode pembayaran **Transfer Bank**. Menampilkan instruksi transfer dan konfirmasi pembayaran.

---

### 7. 💵 Pembayaran — Metode Tunai

**File:** [`Pembayaran - metode tunai.png`](./Pembayaran%20-%20metode%20tunai.png)

Layar konfirmasi pembayaran untuk transaksi tunai. Kasir memasukkan nominal uang yang diterima dan sistem menghitung kembalian otomatis.

---

### 8. 🏦 Pembayaran — Metode Bank

**File:** [`Pembayaran - metode bank.png`](./Pembayaran%20-%20metode%20bank.png)

Layar instruksi transfer bank untuk pelanggan. Menampilkan nomor rekening tujuan dan nominal yang harus ditransfer.

---

### 9. 📷 Pembayaran — Metode QRIS

**File:** [`Pembayaran - metode qris.png`](./Pembayaran%20-%20metode%20qris.png)

Layar pembayaran via **QRIS** (Quick Response Code Indonesian Standard). Menampilkan QR code yang bisa di-scan langsung oleh pelanggan menggunakan aplikasi dompet digital.

---

### 10. 🧾 Detail Transaksi

**File:** [`detail transaksi.png`](./detail%20transaksi.png)

Halaman ringkasan detail transaksi setelah pembayaran berhasil dikonfirmasi. Berfungsi sebagai struk digital.

---

### 11. 🧾 Detail Transaksi — Metode Tunai

**File:** [`detail transaksi - metode tunai.png`](./detail%20transaksi%20-%20metode%20tunai.png)

Detail transaksi spesifik untuk pembayaran tunai, mencantumkan informasi jumlah bayar dan kembalian.

---

### 12. 🧾 Detail Transaksi — Metode QRIS

**File:** [`detail transaksi -  metode qris.png`](./detail%20transaksi%20-%20%20metode%20qris.png)

Detail transaksi untuk pembayaran QRIS, mencantumkan konfirmasi pembayaran digital berhasil.

---

### 13. 🧾 Detail Transaksi — Metode Bank

**File:** [`detail transaksi - metode bank.png`](./detail%20transaksi%20-%20metode%20bank.png)

Detail transaksi untuk pembayaran transfer bank, mencantumkan referensi transfer dan konfirmasi.

---

### 14. 📜 Riwayat Transaksi

**File:** [`Riwayat mobile.png`](./Riwayat%20mobile.png)

Halaman riwayat yang menampilkan daftar semua transaksi yang pernah dilakukan. Pengguna dapat menelusuri histori pesanan sebelumnya.

---

### 15. 🆘 Bantuan — Kendala

**File:** [`bantuan - kendala.png`](./bantuan%20-%20kendala.png)

Halaman bantuan untuk melaporkan kendala atau masalah yang dialami saat menggunakan aplikasi.

---

### 16. ✅ Bantuan — Konfirmasi Pesanan

**File:** [`bantuan - konfirmasi pesanan.png`](./bantuan%20-%20konfirmasi%20pesanan.png)

Halaman konfirmasi untuk membantu pelanggan memastikan detail pesanan sebelum diproses lebih lanjut.

---

### 17. 🔧 Inisialisasi Aplikasi

**File:** [`initialization.png`](./initialization.png)

Tampilan layar inisialisasi aplikasi saat pertama kali disetup atau konfigurasi awal dilakukan.

---

## 📊 Ringkasan Screen

| No  | Screen                       | File                                        | Kategori   |
| :-: | ---------------------------- | ------------------------------------------- | ---------- |
|  1  | Ikon App di Launcher         | `Android Apps.png`                          | Onboarding |
|  2  | Splash Screen                | `Tampilan Awal.png`                         | Onboarding |
|  3  | Beranda                      | `beranda mobile.png`                        | Utama      |
|  4  | Menu Katalog                 | `Menu mobile.png`                           | Utama      |
|  5  | Keranjang — Tunai            | `Keranjang - metode tunai.png`              | Transaksi  |
|  6  | Keranjang — Transfer         | `Keranjang - transfer.png`                  | Transaksi  |
|  7  | Pembayaran — Tunai           | `Pembayaran - metode tunai.png`             | Transaksi  |
|  8  | Pembayaran — Bank            | `Pembayaran - metode bank.png`              | Transaksi  |
|  9  | Pembayaran — QRIS            | `Pembayaran - metode qris.png`              | Transaksi  |
| 10  | Detail Transaksi             | `detail transaksi.png`                      | Transaksi  |
| 11  | Detail Transaksi — Tunai     | `detail transaksi - metode tunai.png`       | Transaksi  |
| 12  | Detail Transaksi — QRIS      | `detail transaksi -  metode qris.png`       | Transaksi  |
| 13  | Detail Transaksi — Bank      | `detail transaksi - metode bank.png`        | Transaksi  |
| 14  | Riwayat Transaksi            | `Riwayat mobile.png`                        | Histori    |
| 15  | Bantuan — Kendala            | `bantuan - kendala.png`                     | Support    |
| 16  | Bantuan — Konfirmasi Pesanan | `bantuan - konfirmasi pesanan.png`          | Support    |
| 17  | Inisialisasi                 | `initialization.png`                        | Setup      |

**Total: 17 screen mockup mobile**

---

## 🔗 Tautan Referensi

| Sumber          | Link                                                                                                                  |
| --------------- | --------------------------------------------------------------------------------------------------------------------- |
| 🎨 **Figma**    | [Buka Desain di Figma](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1) |
| 📁 **Web View** | [`../web/`](../web/)                                                                                                  |
| 📄 **Mockup**   | [`../README.md`](../README.md)                                                                                        |
| 🏠 **Proyek**   | [`../../../../README.md`](../../../../README.md)                                                                      |

---

<div align="center">

**🍗 AYAMO — EAT • CHILL • REPEAT**
Mockup UI/UX Mobile View · Tugas MPPL · 2026

*Dibuat menggunakan [Figma](https://www.figma.com/design/1eqVfEPtoZpmDb4cUy9i60/Ayamo?node-id=6-2&t=FBl8vIPqf5UpQQzH-1) · Diimplementasikan dengan Laravel + Livewire Volt + Tailwind CSS v4*

</div>

