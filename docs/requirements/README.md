# 📚 Indeks Dokumentasi Sistem Ayamo

Selamat datang di pusat dokumentasi teknis dan operasional untuk sistem manajemen UMKM **Ayamo (Ayam Geprek & Fried Chicken)**.

Dokumentasi ini dirancang agar tim pengembang dan agen AI memiliki referensi yang jelas, terpadu, dan konsisten dalam memelihara dan mengembangkan fitur baru.

---

## 📑 Daftar Dokumen

| Dokumen | Deskripsi | Target Pembaca |
| :--- | :--- | :--- |
| 🏗️ [Arsitektur Sistem (`architecture.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/architecture.md) | Penjelasan arsitektur sistem, modular HTTP controllers, Eloquent ORM models, dan struktur direktori. | Developer & AI Agent |
| 🗄️ [Skema Basis Data (`database.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/database.md) | Diagram relasi ERD, spesifikasi tabel, reservasi stok, dan persyaratan penanganan race condition saat checkout. | Database Admin & Developer |
| 📐 [Standar & Konvensi Koding (`standards-and-conventions.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/standards-and-conventions.md) | Pedoman validasi error UI, standar penamaan file, serta spesifikasi token warna (*Design System* Light/Dark & Spice Level). | UI/UX & Developer |
| 📦 [Fitur & Modul Sistem (`features-and-modules.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/features-and-modules.md) | Rincian modul utama: Storefront Web & Mobile, Kasir POS, Katalog & Stok, Pembayaran Transfer/QRIS, Laporan CSV, dan Pengaturan Fleksibel. | Product Owner & Developer |
| 🧪 [Log Eksperimen & Pembelajaran (`pembelajaran.md`)](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/pembelajaran.md) | Catatan debugging, perbaikan bug, arsitektur UX/UI, dan keputusan teknis sepanjang pengembangan. | Tim Pengembang & QA |

---

## 🧭 Alur Akses & Konfigurasi Lokal / Produksi

- **Aplikasi Lokal**: `http://127.0.0.1:8000` (melalui `php artisan serve` atau `composer run dev` di folder `app/`, disertai asset bundler Vite `http://localhost:5173`)
- **Panel Admin**: `http://127.0.0.1:8000/admin` (login via `/admin/login`)
- **Mobile Web View**: `http://127.0.0.1:8000/mobile`
- **Backend Service Root**: Terletak di subfolder `/app` (`app/.env` untuk pengembangan lokal)
- **Konfigurasi Docker Produksi**: Berada di root repositori ([`Dockerfile`](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/Dockerfile), [`docker-compose.yml`](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docker-compose.yml), [`.env.example`](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/.env.example), dan folder [`docker/`](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docker))

---

## 🔄 Siklus Pembaharuan Dokumentasi

1. Setiap ada perubahan arsitektur, controller, middleware, atau infrastruktur container, perbarui [architecture.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/architecture.md).
2. Setiap ada penambahan migrasi atau skema tabel/seeder, perbarui [database.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/database.md).
3. Setiap ada penyesuaian komponen modal/notifikasi, token desain, atau konvensi kode, perbarui [standards-and-conventions.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/docs/requirements/standards-and-conventions.md).
4. Setiap eksperimen atau debugging bermakna dicatat ringkas pada [pembelajaran.md](file:///d:/Project/2tugas/tugas-mppl/Kelompok-17/pembelajaran.md).
5. Jaga agar tidak ada duplikasi informasi yang kontradiktif antar dokumen.
