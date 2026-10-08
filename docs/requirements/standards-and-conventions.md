# 📐 Standar Koding & Konvensi Sistem Ayamo

Dokumen ini adalah pedoman wajib bagi seluruh pengembang (manusia maupun agen AI) yang berkontribusi pada proyek **Ayamo**.

---

## 1. Konvensi Penanganan Error Validasi UI

Dalam menyajikan pesan error dan notifikasi pada antarmuka pengguna (UI), terdapat **2 metode standar** yang wajib diikuti secara konsisten:

### A. Pesan Error Spesifik Input Form (`<x-input-error>`)
Untuk error yang terkait langsung dengan input tertentu (misalnya: *nama menu wajib diisi*, *harga harus angka*, *username sudah digunakan*):
- Letakkan komponen `<x-input-error>` tepat di bawah field input yang bersangkutan.
- **Contoh Penggunaan:**
```blade
<div>
    <label for="name" class="block text-sm font-medium text-sand-700 dark:text-sand-300">Nama Menu</label>
    <input type="text" id="name" wire:model="name" class="w-full rounded-md border-sand-200 dark:border-sand-700 dark:bg-sand-900" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
```

### B. Error Utama / Global Non-Field (`<x-modal-notification>`)
Untuk error kritis yang tidak terikat pada input tunggal (misalnya: *stok bahan baku tidak mencukupi untuk memproses pesanan*, *gagal memproses pembayaran*, *akses transaksi ditolak*):
- Tampilkan menggunakan komponen `<x-modal-notification>`.
- Dipicu melalui session flash message dari controller / Livewire action:
```php
// Di dalam method Livewire atau Controller:
session()->flash('error', 'Gagal memproses transaksi: Saldo kas laci tidak mencukupi.');
```
- Komponen `<x-modal-notification>` pada layout utama akan mendeteksi session `error` dan memunculkan modal dialog peringatan dengan jelas.

---

## 2. Standar Desain & Sistem Token Warna (Design System)

Sistem Ayamo menggunakan palet warna khusus kuliner bertema *warm appetizing* (Gold, Orange, Sand, Green, Red, Sky) dengan dukungan penuh Light Mode & Dark Mode serta badge sistem kepedasan (*Spice Level*).

### A. Primitive Color Palettes

| Palette | 50 / 0 | 100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900 | 950 |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Gold** | `#FFFBEA` | `#FFF3C4` | `#FFE58A` | `#FFD84D` | `#FBBF24` | `#F5A80B` | `#D98606` | `#B36508` | `#8F4D0C` | `#75400F` | `#432105` |
| **Orange** | `#FFF7ED` | `#FFEDD5` | `#FED7AA` | `#FDBA74` | `#FB923C` | `#F97316` | `#EA580C` | `#C2410C` | `#9A3412` | `#7C2D12` | `#431407` |
| **Sand** | `#FFFFFF` (0) / `#FFFBF5` (50) | `#FBF3E4` | `#F1E6D0` | `#DDCFB4` | `#A89877` | `#73664D` | `#645943` | `#4A4132` | `#342C22` | `#221C15` | `#15110C` |
| **Green** | `#F0FDF4` | `#DCFCE7` | `#BBF7D0` | `#86EFAC` | `#4ADE80` | `#22C55E` | `#16A34A` | `#15803D` | `#166534` | `#14532D` | `#052E16` |
| **Red** | `#FEF2F2` | `#FEE2E2` | `#FECACA` | `#FCA5A5` | `#F87171` | `#EF4444` | `#DC2626` | `#B91C1C` | `#991B1B` | `#7F1D1D` | `#450A0A` |
| **Sky** | `#F0F9FF` | `#E0F2FE` | `#BAE6FD` | `#7DD3FC` | `#38BDF8` | `#0EA5E9` | `#0284C7` | `#0369A1` | `#075985` | `#0C4A6E` | `#082F49` |

### B. Semantic Color Tokens

#### 1. Background (`bg-*`)
| Token | Light Mode Value | Dark Mode Value | Kegunaan |
| :--- | :--- | :--- | :--- |
| `canvas` | `sand.50` (`#FFFBF5`) | `sand.950` (`#15110C`) | Latar belakang halaman utama |
| `surface` | `sand.0` (`#FFFFFF`) | `sand.900` (`#221C15`) | Card, container, panel, modal |
| `subtle` | `sand.100` (`#FBF3E4`) | `sand.800` (`#342C22`) | Table row alternate, section subtle |
| `muted` | `sand.200` (`#F1E6D0`) | `sand.700` (`#4A4132`) | Skeleton loader, disabled box |
| `brand-subtle` | `gold.100` (`#FFF3C4`) | `gold.950` (`#432105`) | Highlight promo, badge info brand |
| `inverse` | `sand.900` (`#221C15`) | `sand.50` (`#FFFBF5`) | Tooltip kontras, badge inverse |

#### 2. Text (`text-*`)
| Token | Light Mode Value | Dark Mode Value | Kegunaan |
| :--- | :--- | :--- | :--- |
| `primary` | `sand.900` (`#221C15`) | `sand.50` (`#FFFBF5`) | Heading, body text utama |
| `secondary` | `sand.600` (`#645943`) | `sand.300` (`#DDCFB4`) | Subheading, deskripsi produk |
| `muted` | `sand.500` (`#73664D`) | `sand.400` (`#A89877`) | Timestamp, placeholder, caption |
| `brand` | `orange.700` (`#C2410C`) | `gold.400` (`#FBBF24`) | Brand accent text, nama resto |
| `link` | `orange.700` (`#C2410C`) | `gold.300` (`#FFD84D`) | Text hyperlink |
| `link-hover` | `orange.800` (`#9A3412`) | `gold.200` (`#FFE58A`) | Hyperlink saat cursor hover |
| `on-primary` | `orange.950` (`#431407`) | `orange.950` (`#431407`) | Teks di atas button primary |
| `on-secondary`| `sand.0` (`#FFFFFF`) | `sand.0` (`#FFFFFF`) | Teks di atas button secondary |

#### 3. Border (`border-*`)
| Token | Light Mode Value | Dark Mode Value |
| :--- | :--- | :--- |
| `default` | `sand.200` (`#F1E6D0`) | `sand.700` (`#4A4132`) |
| `strong` | `sand.300` (`#DDCFB4`) | `sand.600` (`#645943`) |
| `brand` | `gold.700` (`#B36508`) | `gold.500` (`#F5A80B`) |
| `focus` | `orange.600` (`#EA580C`) | `gold.400` (`#FBBF24`) |

#### 4. Action / Button (`action-*`)
| Action Type | State | Light Mode | Dark Mode |
| :--- | :--- | :--- | :--- |
| **Primary** | Normal | `gold.400` (`#FBBF24`) | `gold.400` (`#FBBF24`) |
| | Hover | `gold.500` (`#F5A80B`) | `gold.300` (`#FFD84D`) |
| | Active | `gold.600` (`#D98606`) | `gold.200` (`#FFE58A`) |
| **Secondary**| Normal | `orange.700` (`#C2410C`) | `orange.700` (`#C2410C`) |
| | Hover | `orange.800` (`#9A3412`) | `orange.800` (`#9A3412`) |
| | Active | `orange.900` (`#7C2D12`) | `orange.900` (`#7C2D12`) |
| **Ghost Hover**| Hover | `gold.100` (`#FFF3C4`) | `sand.800` (`#342C22`) |

#### 5. Status System (`status-*`)
- **Success**:
  - Light: BG `green.100` (`#DCFCE7`), Border `green.300` (`#86EFAC`), Text `green.800` (`#166534`), Solid `green.600` (`#16A34A`).
  - Dark: BG `green.950` (`#052E16`), Border `green.800` (`#166534`), Text `green.300` (`#86EFAC`), Solid `green.500` (`#22C55E`).
- **Warning**:
  - Light: BG `orange.100` (`#FFEDD5`), Border `orange.300` (`#FDBA74`), Text `orange.800` (`#9A3412`), Solid `orange.600` (`#EA580C`).
  - Dark: BG `orange.950` (`#431407`), Border `orange.800` (`#9A3412`), Text `orange.300` (`#FDBA74`), Solid `orange.500` (`#F97316`).
- **Danger**:
  - Light: BG `red.100` (`#FEE2E2`), Border `red.300` (`#FCA5A5`), Text `red.800` (`#991B1B`), Solid `red.600` (`#DC2626`).
  - Dark: BG `red.950` (`#450A0A`), Border `red.800` (`#991B1B`), Text `red.300` (`#FCA5A5`), Solid `red.500` (`#EF4444`).
- **Info**:
  - Light: BG `sky.100` (`#E0F2FE`), Border `sky.300` (`#7DD3FC`), Text `sky.800` (`#075985`), Solid `sky.600` (`#0284C7`).
  - Dark: BG `sky.950` (`#082F49`), Border `sky.800` (`#075985`), Text `sky.300` (`#7DD3FC`), Solid `sky.500` (`#0EA5E9`).

#### 6. Spice Level Badges (`spice-*`)
Level kepedasan menu ayam geprek diseragamkan dengan token:
- **Level 1 (Manis/Sedang)**: Background `gold.200` (`#FFE58A`), Teks `orange.950` (`#431407`)
- **Level 2 (Pedas Biasa)**: Background `gold.400` (`#FBBF24`), Teks `orange.950` (`#431407`)
- **Level 3 (Pedas Mantap)**: Background `orange.500` (`#F97316`), Teks `orange.950` (`#431407`)
- **Level 4 (Ekstra Pedas)**: Background `orange.700` (`#C2410C`), Teks `sand.0` (`#FFFFFF`)
- **Level 5 (Super Pedas / Samyang Fire)**: Background `red.800` (`#991B1B`), Teks `sand.0` (`#FFFFFF`)

---

## 3. Standar Penamaan File & Berkas

1. **Blade & Livewire Views**:
   - Wajib menggunakan format **`kebab-case.blade.php`**.
   - Contoh:
     - `pos-checkout.blade.php`
     - `menu-table.blade.php`
     - `inventory-stock-in.blade.php`
2. **PHP Classes, Models, & Controllers**:
   - Wajib menggunakan format **`PascalCase.php`**.
   - Contoh:
     - `OrderController.php`
     - `ShopController.php`
     - `Product.php`
     - `Transaction.php`
3. **Database Migrations**:
   - Mengikuti konvensi standar Laravel: `YYYY_MM_DD_HHMMSS_create_table_name_table.php` (snake_case).
4. **Dokumentasi & Perencanaan**:
   - Format file dokumentasi dan rencana kerja: `kebab-case.md` atau `YY-MM-DD-keterangan.md`.

---

## 4. Pedoman Penggunaan AI & Batasan Kode (*No AI Slop*)

Demi menjaga integritas struktur data dan performa jangka panjang:
- **Dilarang Keras AI Slop**: Jangan membuat kode berbelit-belit, boilerplate yang tidak diperlukan, atau struktur yang tidak diuji.
- **Integritas Database**: Skema tabel, migrasi, relasi antar-model, dan seeder harus dirancang secara matang, terstruktur, serta diverifikasi secara cermat sesuai kebutuhan bisnis UMKM Ayamo.
- **Konsistensi Framework**: Selalu gunakan fitur resmi Laravel 12, Livewire Volt, dan Flux UI sesuai dokumentasi resmi.

---

## 5. Standar Kode PHP & Blade

1. **Type Hinting & Return Types**:
   - Selalu sertakan deklarasi tipe data eksplisit pada parameter dan return value fungsi/method:
   ```php
   public function calculateTotal(int $quantity, float $unitPrice, float $discount = 0.0): float
   {
       return ($quantity * $unitPrice) - $discount;
   }
   ```
2. **PHP 8 Constructor Property Promotion**:
   - Gunakan fitur modern constructor promotion untuk efisiensi penulisan class.
3. **PHPDoc & Anotasi**:
   - Utamakan PHPDoc block yang rapi dibandingkan komentar inline yang berlebihan.
4. **Tailwind & Flux Styling**:
   - Gunakan semantic color token di atas (`bg-canvas`, `bg-surface`, `text-primary`, `action-primary`, `border-default`) agar konsistensi tampilan antar modul dan dark mode terjaga.
