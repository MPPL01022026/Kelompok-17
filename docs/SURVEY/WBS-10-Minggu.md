# Work Breakdown Structure Sistem Ayamo 10 Minggu

WBS ini menjadi baseline pekerjaan Sistem Ayamo untuk periode 24 September sampai 2 Desember 2026. Setiap work package terhubung dengan jadwal, effort, biaya, dan risiko pada dokumen rencana pengelolaan proyek.

## 1.0 Manajemen dan Inisiasi Proyek

- **1.1 Kickoff dan pembagian peran**: menetapkan Project Manager, Business Analyst, UI/UX, frontend, backend, DevOps, dan QA. Fazah memimpin frontend dan UI/UX, sedangkan Rafi mendukung frontend sekaligus memimpin pengujian.
- **1.2 Survei dan wawancara UMKM**: mengumpulkan proses bisnis, produk, transaksi, stok, dan kebutuhan statistik.
- **1.3 Dokumen inisiasi**: menyusun Project Charter, Stakeholder Register, dan WBS.
- **1.4 Pengendalian proyek**: memantau jadwal, scope, effort, biaya, risiko, blocker, serta evaluasi setiap sprint.

**Deliverable:** baseline proyek, struktur tim, catatan survei, dan laporan evaluasi sprint.

## 2.0 Analisis Kebutuhan dan Setup

- **2.1 Kebutuhan fungsional dan nonfungsional**: menyusun fitur, keamanan, performa, responsivitas, serta batasan sistem.
- **2.2 Alur bisnis dan acceptance criteria**: mendefinisikan customer journey, proses admin, order, transaksi, dan pelaporan.
- **2.3 Setup pengembangan**: menyiapkan repository, Lark Base, Laravel, database, dan lingkungan kerja.
- **2.4 Persetujuan baseline scope**: memastikan fitur inti dan out-of-scope disepakati.

**Deliverable:** requirement baseline, acceptance criteria, dan starter project.

## 3.0 Perancangan Sistem

- **3.1 UI/UX customer mobile**: merancang beranda, menu, keranjang, pembayaran, riwayat, detail transaksi, dan bantuan.
- **3.2 UI/UX web dan admin**: merancang katalog desktop, login, dashboard, produk, order, transaksi, dan laporan.
- **3.3 ERD dan database**: merancang tabel user, product, order, transaction, detail transaksi, stok, dan pengaturan.
- **3.4 Arsitektur dan standar**: menetapkan struktur controller/model/view, keamanan, validasi, naming, dan error handling.

**Deliverable:** mockup, ERD, dokumen arsitektur, dan standar pengembangan.

## 4.0 Fondasi Aplikasi dan Katalog

- **4.1 Database implementation**: membuat migration, model, relationship, seeder, dan koneksi database.
- **4.2 Autentikasi dan admin awal**: membuat login/logout, session, middleware, throttle, dan dashboard awal.
- **4.3 Manajemen produk**: membuat CRUD produk, kategori, harga, status, foto, dan stok produk.
- **4.4 Customer web**: membuat beranda, katalog, filter, detail produk, dan navigasi customer.

**Deliverable:** database aktif, admin panel awal, produk dapat dikelola, dan katalog dapat diakses.

## 5.0 Customer Flow dan Order

- **5.1 Customer mobile**: mengimplementasikan layout mobile, bottom navigation, katalog, dan riwayat.
- **5.2 Keranjang dan checkout**: membuat session cart, kuantitas, subtotal, total, dan validasi checkout.
- **5.3 Metode pembayaran manual**: mendukung tunai, transfer, dan QRIS tanpa payment gateway otomatis.
- **5.4 Order dan stok**: mengonfirmasi order, mencatat status, dan memperbarui stok secara atomik.

**Deliverable:** alur pemesanan end-to-end dari katalog sampai order terkonfirmasi.

## 6.0 Transaksi Statistik dan Stok

- **6.1 Transaksi penjualan**: mencatat transaksi hasil order dan penjualan manual kasir.
- **6.2 Riwayat transaksi**: menampilkan daftar, filter, dan detail transaksi.
- **6.3 Statistik penjualan**: menghitung omzet, jumlah transaksi, rata-rata belanja, dan produk terlaris.
- **6.4 Inventori dasar**: mencatat stok dan menampilkan peringatan batas minimum.

**Deliverable:** data transaksi yang dapat ditelusuri dan dashboard statistik yang dapat diverifikasi.

## 7.0 Integrasi dan Hardening

- **7.1 Integrasi modul**: menghubungkan customer, admin, order, transaksi, stok, dan laporan.
- **7.2 UI/UX polishing**: memperbaiki konsistensi visual, responsivitas, feedback, dan aksesibilitas dasar.
- **7.3 Security dan performance**: melakukan review otorisasi, CSRF, validasi, query, caching, dan build production.

**Deliverable:** versi beta terintegrasi dan siap diuji.

## 8.0 Pengujian dan UAT

- **8.1 Unit dan feature test**: menguji autentikasi, produk, cart, order, transaksi, dan statistik.
- **8.2 Integration dan regression test**: menguji alur lintas modul, browser, serta ukuran layar.
- **8.3 User Acceptance Test**: memvalidasi sistem bersama pemilik UMKM.
- **8.4 Perbaikan defect**: menangani defect dan penyesuaian hasil UAT berdasarkan prioritas.

**Deliverable:** test result, defect log, bukti UAT, dan release candidate.

## 9.0 Dokumentasi dan Rilis

- **9.1 Dokumentasi akhir**: seluruh anggota menyusun laporan MPPL, user guide, dan dokumentasi teknis sesuai pekerjaan atau modul yang menjadi tanggung jawabnya.
- **9.2 Deployment dan demo**: menyiapkan hosting atau demo lokal, environment, dan seed data.
- **9.3 Penutupan**: melaksanakan presentasi, demonstrasi, serah terima, dan evaluasi akhir.

**Deliverable:** sistem demo, laporan akhir, user guide, dan bukti penutupan proyek.

## Pemetaan Minggu

| Minggu | Kelompok WBS Dominan | Hasil |
|---:|---|---|
| 1 | 1.0 | Inisiasi dan baseline proyek |
| 2 | 2.0 dan awal 3.0 | Requirement dan setup |
| 3 | 3.0 dan awal 4.0 | Desain dan fondasi teknis |
| 4 | 4.0 | Database dan admin awal |
| 5 | 4.0 dan awal 5.0 | Produk dan customer panel |
| 6 | 5.0 | Keranjang, checkout, pembayaran, order |
| 7 | 6.0 | Transaksi, statistik, dan stok |
| 8 | 7.0 | Integrasi dan hardening |
| 9 | 8.0 dan awal 9.0 | Testing, UAT, dan dokumentasi |
| 10 | 8.4 dan 9.0 | Perbaikan, rilis, dan penutupan |
