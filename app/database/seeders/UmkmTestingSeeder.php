<?php

namespace Database\Seeders;

use App\Models\LoginAttempt;
use App\Models\Order;
use App\Models\Settings;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UmkmTestingSeeder extends Seeder
{
    /**
     * Seed seluruh data operasional UMKM Ayamo untuk kebutuhan testing,
     * KECUALI tabel products (tidak menyentuh / menambah / mengubah produk).
     */
    public function run(): void
    {
        $now = Carbon::now();

        $this->seedSettings();
        $this->seedOrdersAndTransactions($now);
        $this->seedLoginAttempts($now);
    }

    private function seedSettings(): void
    {
        Settings::seedDefaults();

        $flexibleSettings = [
            [
                'key' => 'promo_text',
                'value' => 'Gratis ekstra saus sambal & kemasan rapi untuk minimal beli 5 pcs.',
            ],
            [
                'key' => 'instagram_handle',
                'value' => '@ayamocrispy.langsa',
            ],
            [
                'key' => 'free_packaging_min',
                'value' => '5',
            ],
        ];

        foreach ($flexibleSettings as $entry) {
            Settings::query()->updateOrCreate(
                ['key' => $entry['key']],
                ['value' => $entry['value']]
            );
        }
    }

    private function seedOrdersAndTransactions(Carbon $now): void
    {
        $orders = [
            // Hari ini (today) — beragam status untuk uji antrean & dashboard
            [
                'id' => 'AYM-1001',
                'customer_name' => 'Rizky Pratama',
                'phone' => '081260011001',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Sambal pisah, dada potong kecil',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 4],
                ],
                'timestamp' => $now->copy()->subMinutes(25),
            ],
            [
                'id' => 'AYM-1002',
                'customer_name' => 'Siti Nurhaliza',
                'phone' => '085260011002',
                'payment_method' => 'cod',
                'status' => 'confirmed',
                'note' => 'Ambil jam 19.30',
                'items' => [
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 3],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 6],
                ],
                'timestamp' => $now->copy()->subHours(2),
            ],
            [
                'id' => 'AYM-1003',
                'customer_name' => 'Ahmad Fauzi',
                'phone' => '089560011003',
                'payment_method' => 'whatsapp',
                'status' => 'pending',
                'note' => 'Mohon goreng garing ya kak',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'hati-ayam', 'name' => 'Hati Ayam', 'price' => 2000, 'quantity' => 3],
                ],
                'timestamp' => $now->copy()->subMinutes(12),
            ],
            [
                'id' => 'AYM-1004',
                'customer_name' => 'Putri Andini',
                'phone' => '082160011004',
                'payment_method' => 'cod',
                'status' => 'processing',
                'note' => 'Tanpa saus cabai',
                'items' => [
                    ['product_id' => 'paha-bawah', 'name' => 'Paha Bawah', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 5],
                ],
                'timestamp' => $now->copy()->subMinutes(40),
            ],
            [
                'id' => 'AYM-1005',
                'customer_name' => 'Budi Santoso',
                'phone' => '081360011005',
                'payment_method' => 'whatsapp',
                'status' => 'ready',
                'note' => 'Sudah transfer BSI',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 4],
                    ['product_id' => 'leher-ayam', 'name' => 'Leher Ayam', 'price' => 5000, 'quantity' => 1],
                ],
                'timestamp' => $now->copy()->subHour(),
            ],

            // Rentang 7 hari terakhir (7d)
            [
                'id' => 'AYM-1006',
                'customer_name' => 'Nadia Safira',
                'phone' => '085360011006',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Paket mahasiswa malam',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 5],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 10],
                ],
                'timestamp' => $now->copy()->subDay()->setHour(19)->setMinute(15),
            ],
            [
                'id' => 'AYM-1007',
                'customer_name' => 'Farhan Maulana',
                'phone' => '089660011007',
                'payment_method' => 'cod',
                'status' => 'confirmed',
                'note' => 'Tambahkan tisu lebih',
                'items' => [
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'paha-bawah', 'name' => 'Paha Bawah', 'price' => 7000, 'quantity' => 2],
                ],
                'timestamp' => $now->copy()->subDays(2)->setHour(20)->setMinute(30),
            ],
            [
                'id' => 'AYM-1008',
                'customer_name' => 'Dewi Kartika',
                'phone' => '082260011008',
                'payment_method' => 'cod',
                'status' => 'cancelled',
                'note' => 'Dibatalkan pelanggan (ubah jadwal)',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 3],
                ],
                'timestamp' => $now->copy()->subDays(3)->setHour(18)->setMinute(10),
            ],
            [
                'id' => 'AYM-1009',
                'customer_name' => 'Hendra Wijaya',
                'phone' => '081160011009',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Pesanan rapat kosan',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 6],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 10],
                ],
                'timestamp' => $now->copy()->subDays(4)->setHour(21)->setMinute(5),
            ],
            [
                'id' => 'AYM-1010',
                'customer_name' => 'Larasati Ayu',
                'phone' => '085760011010',
                'payment_method' => 'cod',
                'status' => 'pending',
                'note' => 'Bumbu tabur sedang',
                'items' => [
                    ['product_id' => 'hati-ayam', 'name' => 'Hati Ayam', 'price' => 2000, 'quantity' => 5],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 8],
                ],
                'timestamp' => $now->copy()->subDays(5)->setHour(17)->setMinute(45),
            ],

            // Rentang 30 hari terakhir (month / 30d)
            [
                'id' => 'AYM-1011',
                'customer_name' => 'Reza Pahlevi',
                'phone' => '081260011011',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Acara kumpul UKM',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 8],
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 6],
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 6],
                ],
                'timestamp' => $now->copy()->subDays(9)->setHour(19)->setMinute(50),
            ],
            [
                'id' => 'AYM-1012',
                'customer_name' => 'Maya Puspita',
                'phone' => '085260011012',
                'payment_method' => 'cod',
                'status' => 'confirmed',
                'note' => '',
                'items' => [
                    ['product_id' => 'paha-bawah', 'name' => 'Paha Bawah', 'price' => 7000, 'quantity' => 4],
                    ['product_id' => 'leher-ayam', 'name' => 'Leher Ayam', 'price' => 5000, 'quantity' => 2],
                ],
                'timestamp' => $now->copy()->subDays(12)->setHour(20)->setMinute(20),
            ],
            [
                'id' => 'AYM-1013',
                'customer_name' => 'Taufik Hidayat',
                'phone' => '089560011013',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Jangan terlalu pedas',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 5],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 8],
                ],
                'timestamp' => $now->copy()->subDays(16)->setHour(18)->setMinute(40),
            ],
            [
                'id' => 'AYM-1014',
                'customer_name' => 'Intan Permata',
                'phone' => '082160011014',
                'payment_method' => 'cod',
                'status' => 'cancelled',
                'note' => 'Timeout konfirmasi',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 2],
                ],
                'timestamp' => $now->copy()->subDays(20)->setHour(16)->setMinute(55),
            ],
            [
                'id' => 'AYM-1015',
                'customer_name' => 'Bagas Prakoso',
                'phone' => '081360011015',
                'payment_method' => 'whatsapp',
                'status' => 'confirmed',
                'note' => 'Pesanan keluarga akhir pekan',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 4],
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 4],
                    ['product_id' => 'hati-ayam', 'name' => 'Hati Ayam', 'price' => 2000, 'quantity' => 4],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 10],
                ],
                'timestamp' => $now->copy()->subDays(24)->setHour(21)->setMinute(10),
            ],
        ];

        $manualTransactions = [
            [
                'id' => 'TRX-POS-1001',
                'customer_name' => 'Pembeli Gerai #1',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 3],
                ],
                'timestamp' => $now->copy()->subMinutes(18),
            ],
            [
                'id' => 'TRX-POS-1002',
                'customer_name' => 'Pembeli Gerai #2',
                'items' => [
                    ['product_id' => 'paha-bawah', 'name' => 'Paha Bawah', 'price' => 7000, 'quantity' => 3],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 4],
                ],
                'timestamp' => $now->copy()->subHours(3),
            ],
            [
                'id' => 'TRX-POS-1003',
                'customer_name' => 'Ojek Online Walk-in',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 4],
                ],
                'timestamp' => $now->copy()->subDay()->setHour(16)->setMinute(30),
            ],
            [
                'id' => 'TRX-POS-1004',
                'customer_name' => 'Mahasiswa Sekitar Gerai',
                'items' => [
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 2],
                    ['product_id' => 'hati-ayam', 'name' => 'Hati Ayam', 'price' => 2000, 'quantity' => 2],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 5],
                ],
                'timestamp' => $now->copy()->subDays(2)->setHour(22)->setMinute(5),
            ],
            [
                'id' => 'TRX-POS-1005',
                'customer_name' => 'Pembeli Gerai Malam',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 3],
                    ['product_id' => 'leher-ayam', 'name' => 'Leher Ayam', 'price' => 5000, 'quantity' => 2],
                ],
                'timestamp' => $now->copy()->subDays(4)->setHour(23)->setMinute(10),
            ],
            [
                'id' => 'TRX-POS-1006',
                'customer_name' => 'Pelanggan Tetap Gerai',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 6],
                    ['product_id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'price' => 500, 'quantity' => 12],
                ],
                'timestamp' => $now->copy()->subDays(6)->setHour(19)->setMinute(0),
            ],
            [
                'id' => 'TRX-POS-1007',
                'customer_name' => 'Kasir Shift Sore',
                'items' => [
                    ['product_id' => 'paha-bawah', 'name' => 'Paha Bawah', 'price' => 7000, 'quantity' => 5],
                ],
                'timestamp' => $now->copy()->subDays(10)->setHour(17)->setMinute(25),
            ],
            [
                'id' => 'TRX-POS-1008',
                'customer_name' => 'Karyawan Sekitar Jalan',
                'items' => [
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'price' => 7000, 'quantity' => 4],
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 3],
                ],
                'timestamp' => $now->copy()->subDays(14)->setHour(20)->setMinute(45),
            ],
            [
                'id' => 'TRX-POS-1009',
                'customer_name' => 'Walk-in Keluarga',
                'items' => [
                    ['product_id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'price' => 7000, 'quantity' => 3],
                    ['product_id' => 'hati-ayam', 'name' => 'Hati Ayam', 'price' => 2000, 'quantity' => 4],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'price' => 1000, 'quantity' => 6],
                ],
                'timestamp' => $now->copy()->subDays(18)->setHour(18)->setMinute(50),
            ],
            [
                'id' => 'TRX-POS-1010',
                'customer_name' => 'Penutupan Shift Malam',
                'items' => [
                    ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'price' => 7000, 'quantity' => 5],
                    ['product_id' => 'leher-ayam', 'name' => 'Leher Ayam', 'price' => 5000, 'quantity' => 3],
                ],
                'timestamp' => $now->copy()->subDays(22)->setHour(23)->setMinute(30),
            ],
        ];

        Model::withoutTimestamps(function () use ($orders, $manualTransactions): void {
            foreach ($orders as $row) {
                $total = collect($row['items'])->sum(fn(array $item): int => $item['price'] * $item['quantity']);
                $itemsJson = json_encode($row['items'], JSON_THROW_ON_ERROR);
                $reserved = $row['status'] !== 'cancelled';
                $ts = $row['timestamp'];

                $order = Order::query()->find($row['id']) ?? new Order(['id' => $row['id']]);
                $order->forceFill([
                    'id' => $row['id'],
                    'customer_name' => $row['customer_name'],
                    'phone' => $row['phone'],
                    'items' => $itemsJson,
                    'total' => $total,
                    'note' => $row['note'],
                    'payment_method' => $row['payment_method'],
                    'status' => $row['status'],
                    'stock_reserved' => $reserved,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ])->save();

                if ($row['status'] === 'confirmed') {
                    $txId = 'order-' . $row['id'];
                    $tx = Transaction::query()->find($txId) ?? new Transaction(['id' => $txId]);
                    $tx->forceFill([
                        'id' => $txId,
                        'source' => 'online',
                        'customer_name' => $row['customer_name'],
                        'items' => $itemsJson,
                        'total' => $total,
                        'created_at' => $ts,
                        'updated_at' => $ts,
                    ])->save();
                } else {
                    Transaction::query()->where('id', 'order-' . $row['id'])->delete();
                }
            }

            foreach ($manualTransactions as $row) {
                $total = collect($row['items'])->sum(fn(array $item): int => $item['price'] * $item['quantity']);
                $itemsJson = json_encode($row['items'], JSON_THROW_ON_ERROR);
                $ts = $row['timestamp'];

                $tx = Transaction::query()->find($row['id']) ?? new Transaction(['id' => $row['id']]);
                $tx->forceFill([
                    'id' => $row['id'],
                    'source' => 'manual',
                    'customer_name' => $row['customer_name'],
                    'items' => $itemsJson,
                    'total' => $total,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ])->save();
            }
        });
    }

    private function seedLoginAttempts(Carbon $now): void
    {
        // Pastikan akun utama admin tidak sedang terkunci saat testing dimulai
        LoginAttempt::query()->where('identifier', 'admin')->delete();

        $samples = [
            [
                'identifier' => 'kasir_uji_peringatan',
                'attempts' => 2,
                'locked_until' => null,
            ],
            [
                'identifier' => 'kasir_uji_terkunci',
                'attempts' => 5,
                'locked_until' => $now->copy()->addMinutes(15),
            ],
        ];

        foreach ($samples as $sample) {
            LoginAttempt::query()->updateOrCreate(
                ['identifier' => $sample['identifier']],
                [
                    'attempts' => $sample['attempts'],
                    'locked_until' => $sample['locked_until'],
                ]
            );
        }
    }
}

