# Roadmap Pengembangan Sistem Ayamo 10 Minggu

Roadmap ini menerjemahkan WBS proyek menjadi sepuluh sprint mingguan dari 24 September sampai 2 Desember 2026. Pekerjaan yang telah selesai tetap dicatat pada minggu pelaksanaannya, sedangkan pekerjaan lanjutan ditempatkan setelah fondasi yang diperlukan tersedia.

## Ringkasan Roadmap

| Sprint | Periode | Sasaran | Aktivitas Utama | Exit Criteria |
|---:|---|---|---|---|
| 1 | 24-30 September | Inisiasi proyek | Survei, wawancara, peran, workspace, charter, stakeholder, WBS | Scope awal dan struktur tim disetujui |
| 2 | 1-7 Oktober | Kebutuhan dan setup | FR/NFR, alur bisnis, acceptance criteria, Laravel setup | Baseline kebutuhan dibekukan |
| 3 | 8-14 Oktober | Desain sistem | UI/UX, ERD, arsitektur, keamanan, standar teknis | Baseline desain siap diimplementasikan |
| 4 | 15-21 Oktober | Fondasi aplikasi | Migration, model, seeder, login, session, admin dashboard | Admin dapat masuk dan membaca data dasar |
| 5 | 22-28 Oktober | Katalog dan customer panel | Produk, kategori, stok produk, katalog web/mobile | Produk dapat dikelola dan dilihat pelanggan |
| 6 | 29 Oktober-4 November | Pemesanan | Keranjang, checkout, metode bayar manual, konfirmasi order | Pesanan dapat dibuat dan diproses |
| 7 | 5-11 November | Transaksi dan analitik | Transaksi manual, riwayat, statistik, inventori dasar | Data penjualan menghasilkan laporan yang dapat diverifikasi |
| 8 | 12-18 November | Integrasi | Integrasi modul, UI polishing, security, performance | Versi beta stabil tersedia |
| 9 | 19-25 November | Testing dan UAT | Unit/feature test, regresi, cross-device, UAT | Tidak ada defect kritis dan masukan UAT tercatat |
| 10 | 26 November-2 Desember | Rilis | Bug fixing, dokumentasi, deployment, demo, serah terima | Release candidate, laporan, dan demo selesai |

## Milestone

| Milestone | Tanggal | Bukti |
|---|---|---|
| Inisiasi selesai | 30 September 2026 | Charter, stakeholder, WBS, workspace |
| Kebutuhan disetujui | 7 Oktober 2026 | Requirement dan acceptance criteria |
| Desain selesai | 14 Oktober 2026 | Mockup, ERD, arsitektur |
| Alpha tersedia | 6 November 2026 | Katalog, admin, order, pembayaran, stok inti |
| Beta terintegrasi | 18 November 2026 | Seluruh modul inti terhubung |
| UAT selesai | 25 November 2026 | Catatan penerimaan dan defect log |
| Rilis proyek | 2 Desember 2026 | Sistem demo, dokumentasi, presentasi |

## Aturan Pelaksanaan Sprint

- Sprint berdurasi satu minggu dan ditutup dengan review singkat.
- Aktivitas hanya masuk sprint jika memiliki PIC, estimasi, dependensi, dan acceptance criteria.
- Pekerjaan belum selesai dikembalikan ke backlog dan diprioritaskan ulang; tidak otomatis dianggap selesai.
- Perubahan ruang lingkup harus mencatat dampak terhadap jadwal, manday, biaya, dan risiko.
- Definition of Done minimal mencakup implementasi, review, pengujian relevan, dan pembaruan dokumentasi oleh anggota yang mengerjakan aktivitas tersebut.
- UAT dilakukan pada build yang telah melewati regression test dan tidak memiliki defect kritis terbuka.

## Struktur Lark Base

| Tabel atau View | Isi |
|---|---|
| Master Schedule | WBS, aktivitas, tanggal, dependensi, milestone, PIC, dan status |
| Sprint 1 sampai Sprint 10 | Aktivitas yang dipilih untuk minggu terkait |
| Sprint Board | Kanban Backlog, To Do, In Progress, Review, Blocked, dan Done |
| Resource and Effort | Anggota, peran, manday rencana, dan manday aktual |
| Project Cost | Biaya tenaga kerja, layanan, operasional, overhead, dan cadangan |
| Risk Register | Risiko, skor, strategi, mitigasi, pemilik, dan indikator |

[Buka Lark Base Proyek Ayamo](https://zjpwcuecoylf.jp.larksuite.com/base/Zl4qbsdyeaYoJwsvF6zjJ24Cp5b?from=from_copylink)
