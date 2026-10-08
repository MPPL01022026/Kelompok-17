# 📚 Indeks Dokumentasi Sistem Ayamo

Selamat datang di pusat dokumentasi teknis dan operasional untuk sistem manajemen UMKM **Ayamo (Ayam Geprek & Fried Chicken)**.

Dokumentasi ini dirancang agar tim pengembang dan agen AI memiliki referensi yang jelas, terpadu, dan konsisten dalam memelihara dan mengembangkan fitur baru.

---

## 📑 Daftar Dokumen

| Dokumen | Deskripsi | Target Pembaca |
| :--- | :--- | :--- |
| 🏗️ [Arsitektur Sistem (`architecture.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/architecture.md) | Penjelasan arsitektur sistem, modular HTTP controllers, Eloquent ORM models, dan struktur direktori. | Developer & AI Agent |
| 🗄️ [Skema Basis Data (`database.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/database.md) | Diagram relasi ERD, spesifikasi tabel (`products`, `orders`, `transactions`, `login_attempts`, `users`), dan tipe data. | Database Admin & Developer |
| 📐 [Standar & Konvensi Koding (`standards-and-conventions.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/standards-and-conventions.md) | Pedoman validasi error UI, standar penamaan file, serta spesifikasi token warna (*Design System* Light/Dark & Spice Level). | UI/UX & Developer |
| 📦 [Fitur & Modul Sistem (`features-and-modules.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/features-and-modules.md) | Rincian modul utama: POS/Kasir, Katalog Menu, Stok & Inventaris, Laporan Omzet, dan Manajemen Akun. | Product Owner & Developer |

---

## 🧭 Alur Akses & Port Lokal

- **Aplikasi Web**: `http://localhost:5173` (atau `http://127.0.0.1:8000` saat menggunakan `artisan serve`)
- **Backend Service Root**: Terletak di subfolder `/app`
- **Konvensi Konfigurasi**: Menggunakan file `.env` di dalam direktori `app/`

---

## 🔄 Siklus Pembaharuan Dokumentasi

1. Setiap ada perubahan arsitektur atau controller/model baru, perbarui [architecture.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/architecture.md).
2. Setiap ada penambahan migrasi atau skema tabel, perbarui [database.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/database.md).
3. Setiap ada penyesuaian token desain atau konvensi kode, perbarui [standards-and-conventions.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/standards-and-conventions.md).
4. Jaga agar tidak ada duplikasi informasi yang kontradiktif antar dokumen.
