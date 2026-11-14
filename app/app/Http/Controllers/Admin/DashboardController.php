<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        Product::seedDefaults();
        Settings::seedDefaults();

        $tab = $request->query('tab', 'overview');
        $period = $request->query('period', 'day');

        abort_unless(in_array($tab, ['overview', 'orders', 'products', 'reports', 'settings'], true), 404);
        abort_unless(in_array($period, ['day', 'week', 'month'], true), 400);

        $orders = Order::orderByDesc('created_at')->limit(500)->get();
        [$products, $archivedProducts] = $this->loadNormalizedCatalog();
        $transactions = Transaction::orderBy('created_at')->get();
        $recentTransactions = Transaction::orderByDesc('created_at')->limit(8)->get();
        $settings = Settings::getMap();
        $settingRows = Settings::query()->orderBy('key')->get();
        $protectedSettingKeys = Settings::protectedKeys();
        $paymentChannels = Settings::getPaymentChannels($settings);

        $stats = $this->buildOverviewStats($period, $orders, $products, $archivedProducts, $transactions);
        $report = $this->buildUmkmReport($request, $orders, $products, $archivedProducts, $transactions);

        return view('admin.dashboard', compact(
            'tab',
            'period',
            'orders',
            'products',
            'archivedProducts',
            'recentTransactions',
            'settings',
            'settingRows',
            'protectedSettingKeys',
            'paymentChannels',
            'stats',
            'report'
        ));
    }

    public function exportReport(Request $request): StreamedResponse
    {
        Product::seedDefaults();
        Settings::seedDefaults();

        $orders = Order::orderByDesc('created_at')->limit(1000)->get();
        [$products, $archivedProducts] = $this->loadNormalizedCatalog();
        $transactions = Transaction::orderBy('created_at')->get();
        $settings = Settings::getMap();

        $report = $this->buildUmkmReport($request, $orders, $products, $archivedProducts, $transactions);
        $filename = 'laporan-umkm-ayamo-' . now()->format('Ymd') . '.csv';

        $escapeCell = static function (mixed $value): string {
            $str = (string) $value;
            if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                return "'" . $str;
            }

            return $str;
        };

        return response()->streamDownload(function () use ($report, $settings, $escapeCell): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM agar Excel di Windows langsung membaca karakter dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            $writeRow = static fn (array $row) => fputcsv($handle, array_map($escapeCell, $row));

            $writeRow(['LAPORAN KEUANGAN & OPERASIONAL UMKM — ' . ($settings['store_name'] ?? 'AYAMO')]);
            $writeRow(['Periode', $report['label'] . ' (' . $report['start_date'] . ' s.d. ' . $report['end_date'] . ')']);
            $writeRow(['Filter Sumber', match ($report['source']) {
                'online' => 'Pesanan Online (Web & Mobile)',
                'manual' => 'Kasir Manual (Walk-in)',
                default => 'Semua Sumber',
            }]);
            $writeRow(['Diekspor Pada', now()->format('Y-m-d H:i:s')]);
            $writeRow([]);

            $writeRow(['RINGKASAN EKSEKUTIF UMKM', 'Nilai']);
            $writeRow(['Total Omzet Terkonfirmasi (Rp)', $report['total_revenue']]);
            $writeRow(['Jumlah Transaksi', $report['total_transactions']]);
            $writeRow(['Total Porsi Terjual', $report['total_units']]);
            $writeRow(['Rata-rata Nilai Transaksi (Rp)', $report['avg_order_value']]);
            $writeRow(['Omzet Online Web/Mobile (Rp)', $report['online_revenue']]);
            $writeRow(['Omzet Kasir Manual (Rp)', $report['manual_revenue']]);
            $writeRow(['Potensi Pesanan Berjalan (Rp)', $report['pipeline_revenue']]);
            $writeRow(['Pesanan Berjalan (Unit Order)', $report['pipeline_orders']]);
            $writeRow(['Rasio Konversi Pesanan (%)', $report['conversion_rate']]);
            $writeRow(['Valuasi Stok Katalog Aktif (Rp)', $report['inventory_value']]);
            $writeRow([]);

            $writeRow(['PERFORMA PER MENU UMKM']);
            $writeRow(['Menu', 'Kategori', 'Porsi Terjual', 'Omzet (Rp)', 'Kontribusi (%)', 'Sisa Stok']);
            foreach ($report['product_breakdown'] as $prod) {
                $writeRow([
                    $prod['name'],
                    $prod['category'],
                    $prod['units'],
                    $prod['revenue'],
                    $prod['share_pct'],
                    $prod['stock'],
                ]);
            }
            $writeRow([]);

            $writeRow(['REKAP BUKU KAS HARIAN']);
            $writeRow(['Tanggal', 'Hari/Label', 'Transaksi Online', 'Transaksi Kasir', 'Porsi Terjual', 'Omzet Harian (Rp)']);
            foreach ($report['daily_rows'] as $daily) {
                $writeRow([
                    $daily['date'],
                    $daily['label'],
                    $daily['online_count'],
                    $daily['manual_count'],
                    $daily['units'],
                    $daily['revenue'],
                ]);
            }
            $writeRow([]);

            $writeRow(['RINCIAN TRANSAKSI BUKU KAS']);
            $writeRow(['ID Transaksi', 'Waktu', 'Sumber', 'Pelanggan', 'Rincian Item', 'Jumlah Porsi', 'Nominal (Rp)']);
            foreach ($report['transactions'] as $tx) {
                $writeRow([
                    $tx['id'],
                    $tx['time_label'],
                    $tx['source'] === 'manual' ? 'Kasir Manual' : 'Online',
                    $tx['customer_name'],
                    $tx['items_summary'],
                    $tx['units'],
                    $tx['total'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:80'],
            'store_tagline' => ['required', 'string', 'max:120'],
            'store_time' => ['required', 'string', 'max:60'],
            'phone_number' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:250'],
            'bank_name' => ['required_without:transfer_channels', 'nullable', 'string', 'max:100'],
            'bank_account_number' => ['required_without:transfer_channels', 'nullable', 'string', 'max:50'],
            'bank_account_holder' => ['required_without:transfer_channels', 'nullable', 'string', 'max:100'],
            'admin_key' => ['nullable', 'string', 'max:100'],
            'maintenance_mode' => ['nullable', 'in:true,false,1,0,auto,open,closed'],
            'store_closed_reason' => ['nullable', 'string', 'max:250'],
            'payment_channel' => ['nullable', 'string', 'max:10000'],
            'payment_channel_form' => ['nullable', 'in:1'],
            'transfer_channels' => ['nullable', 'array', 'max:20'],
            'transfer_channels.*.key' => ['nullable', 'string', 'max:50'],
            'transfer_channels.*.enable' => ['nullable'],
            'transfer_channels.*.name' => ['nullable', 'string', 'max:100'],
            'transfer_channels.*.number' => ['nullable', 'string', 'max:60'],
            'transfer_channels.*.holder' => ['nullable', 'string', 'max:100'],
            'qris_enable' => ['nullable'],
            'qris_name' => ['nullable', 'string', 'max:80'],
            'qris_image' => ['nullable', 'string', 'max:255'],
            'qris_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'wa_enable' => ['nullable'],
            'wa_message' => ['nullable', 'string', 'max:300'],
        ]);

        $saveData = [
            'store_name' => trim((string) $data['store_name']),
            'store_tagline' => trim((string) $data['store_tagline']),
            'store_time' => trim((string) $data['store_time']),
            'phone_number' => trim((string) $data['phone_number']),
            'address' => trim((string) $data['address']),
        ];

        $hasStructuredChannels = $request->boolean('payment_channel_form') || $request->has('transfer_channels');
        if ($hasStructuredChannels) {
            $channelResult = $this->buildStructuredPaymentChannels($request, $data, $saveData['store_name'], $saveData['phone_number']);
            if ($channelResult['error'] !== null) {
                return back()
                    ->withInput()
                    ->withErrors([$channelResult['field'] => $channelResult['error']])
                    ->with('error', $channelResult['error']);
            }

            $saveData['bank_name'] = $channelResult['primary']['name'];
            $saveData['bank_account_number'] = $channelResult['primary']['number'];
            $saveData['bank_account_holder'] = $channelResult['primary']['holder'];
            $saveData['payment_channel'] = $channelResult['json'];
        } else {
            $saveData['bank_name'] = trim((string) ($data['bank_name'] ?? ''));
            $saveData['bank_account_number'] = trim((string) ($data['bank_account_number'] ?? ''));
            $saveData['bank_account_holder'] = trim((string) ($data['bank_account_holder'] ?? ''));

            if (array_key_exists('payment_channel', $data) && trim((string) $data['payment_channel']) !== '') {
                $decodedChannel = json_decode((string) $data['payment_channel'], true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedChannel)) {
                    return back()
                        ->withInput()
                        ->withErrors(['payment_channel' => 'Format JSON payment_channel tidak valid.'])
                        ->with('error', 'Format JSON payment_channel tidak valid.');
                }
                $saveData['payment_channel'] = json_encode($decodedChannel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (! $request->has('payment_channel')) {
                $currentMap = Settings::getMap();
                $decodedExisting = json_decode((string) ($currentMap['payment_channel'] ?? ''), true);
                if (is_array($decodedExisting) && isset($decodedExisting['bsi']) && is_array($decodedExisting['bsi'])) {
                    $manualKeys = array_filter(array_keys($decodedExisting), static fn (string $k): bool => ! in_array($k, ['qris', 'whatsapp'], true));
                    if (count($manualKeys) <= 1) {
                        $decodedExisting['bsi']['name'] = $saveData['bank_name'];
                        $decodedExisting['bsi']['number'] = $saveData['bank_account_number'];
                        $decodedExisting['bsi']['holder'] = $saveData['bank_account_holder'];
                        $saveData['payment_channel'] = json_encode($decodedExisting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                }
            }
        }

        if ($request->has('maintenance_mode')) {
            $rawMaint = strtolower(trim((string) ($data['maintenance_mode'] ?? 'false')));
            $saveData['maintenance_mode'] = match (true) {
                in_array($rawMaint, ['1', 'true', 'closed'], true) => 'true',
                $rawMaint === 'auto' => 'auto',
                default => 'false',
            };
        }

        if ($request->has('store_closed_reason')) {
            $reason = trim((string) ($data['store_closed_reason'] ?? ''));
            if ($reason !== '') {
                $saveData['store_closed_reason'] = $reason;
            } else {
                Settings::query()->where('key', 'store_closed_reason')->delete();
            }
        }

        if ($request->has('admin_key') && trim((string) ($data['admin_key'] ?? '')) !== '') {
            $saveData['admin_key'] = trim((string) $data['admin_key']);
        }

        foreach ($saveData as $key => $value) {
            Settings::query()->updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        return redirect('/admin?tab=settings')->with('status', 'Pengaturan toko disimpan.');
    }

    /**
     * Build validated `payment_channel` JSON and synced primary bank fields from structured form input.
     *
     * @param  array<string, mixed>  $data
     * @return array{error: ?string, field: string, primary: array{name: string, number: string, holder: string}, json: string}
     */
    private function buildStructuredPaymentChannels(Request $request, array $data, string $storeName, string $phoneNumber): array
    {
        $emptyResult = static fn (string $field, string $error): array => [
            'error' => $error,
            'field' => $field,
            'primary' => ['name' => '', 'number' => '', 'holder' => ''],
            'json' => '{}',
        ];

        $isTruthy = static fn (mixed $v): bool => in_array((string) $v, ['1', 'true', 'on', 'yes'], true);
        $rawChannels = is_array($request->input('transfer_channels')) ? $request->input('transfer_channels') : [];

        $normalizedTransfers = [];
        $usedKeys = ['qris', 'whatsapp'];
        foreach (array_values($rawChannels) as $idx => $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? ''));
            $number = trim((string) ($row['number'] ?? ''));
            $holder = trim((string) ($row['holder'] ?? ''));
            if ($name === '' && $number === '' && $holder === '') {
                continue;
            }
            if ($name === '' || $number === '') {
                return $emptyResult('transfer_channels', 'Nama bank/e-wallet dan nomor rekening wajib diisi untuk tiap rekening transfer.');
            }

            $baseKey = trim((string) ($row['key'] ?? ''));
            $slugKey = \Illuminate\Support\Str::slug($baseKey !== '' ? $baseKey : $name, '_');
            if ($slugKey === '' || in_array($slugKey, ['qris', 'whatsapp'], true)) {
                $slugKey = 'bank_' . ($idx + 1);
            }
            $uniqueKey = $slugKey;
            $suffix = 2;
            while (in_array($uniqueKey, $usedKeys, true)) {
                $uniqueKey = $slugKey . '_' . $suffix;
                $suffix++;
            }
            $usedKeys[] = $uniqueKey;

            $normalizedTransfers[$uniqueKey] = [
                'enable' => $isTruthy($row['enable'] ?? '0'),
                'type' => 'manual',
                'name' => $name,
                'number' => $number,
                'holder' => $holder !== '' ? $holder : ($storeName ?: 'Ayamo'),
            ];
        }

        if ($normalizedTransfers === []) {
            return $emptyResult('transfer_channels', 'Minimal satu rekening transfer / bank digital harus diisi.');
        }

        $qrisEnable = $isTruthy($request->input('qris_enable', '0'));
        $qrisName = trim((string) ($data['qris_name'] ?? '')) ?: 'QRIS';
        $qrisImage = trim((string) ($data['qris_image'] ?? ''));

        if ($qrisImage !== '' && ! preg_match('#^(https?://|/)#i', $qrisImage)) {
            return $emptyResult('qris_image', 'URL gambar QRIS harus diawali `/` atau `https://`.');
        }

        if ($request->hasFile('qris_image_file')) {
            $file = $request->file('qris_image_file');
            $uploadDir = public_path('img/uploads');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = 'qris-' . time() . '-' . \Illuminate\Support\Str::random(6) . '.' . $ext;
            $file->move($uploadDir, $filename);
            $qrisImage = '/img/uploads/' . $filename;
        }

        $waDigits = preg_replace('/[^0-9]/', '', $phoneNumber);
        if ($waDigits !== '' && str_starts_with($waDigits, '0')) {
            $waDigits = '62' . substr($waDigits, 1);
        }
        $currentChannels = Settings::getPaymentChannels();
        $waUrl = $waDigits !== '' ? ('https://wa.me/' . $waDigits) : $currentChannels['whatsapp']['url'];
        $waEnable = $isTruthy($request->input('wa_enable', '0'));
        $waMessage = trim((string) ($data['wa_message'] ?? '')) ?: 'Halo, saya ingin memesan Ayamo!';

        $payload = array_merge(
            [
                'qris' => [
                    'enable' => $qrisEnable,
                    'type' => 'image',
                    'name' => $qrisName,
                    'image' => $qrisImage,
                ],
            ],
            $normalizedTransfers,
            [
                'whatsapp' => [
                    'enable' => $waEnable,
                    'type' => 'button',
                    'name' => 'WhatsApp',
                    'url' => $waUrl,
                    'message' => $waMessage,
                ],
            ]
        );

        $primary = null;
        foreach ($normalizedTransfers as $ch) {
            if ($ch['enable']) {
                $primary = $ch;
                break;
            }
        }
        $primary ??= reset($normalizedTransfers);

        return [
            'error' => null,
            'field' => '',
            'primary' => [
                'name' => (string) $primary['name'],
                'number' => (string) $primary['number'],
                'holder' => (string) $primary['holder'],
            ],
            'json' => (string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
    }

    public function upsertSettingKey(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:80', 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'value' => ['nullable', 'string', 'max:5000'],
        ], [
            'key.required' => 'Nama key wajib diisi.',
            'key.regex' => 'Key hanya boleh berisi huruf, angka, garis bawah (_), strip (-), atau titik (.).',
        ]);

        $key = trim($data['key']);
        $value = trim((string) ($data['value'] ?? ''));

        if ($key === 'payment_channel' && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return back()
                    ->withInput()
                    ->withErrors(['kv_value' => 'Nilai payment_channel harus berupa JSON object yang valid.'])
                    ->with('error', 'Nilai payment_channel harus berupa JSON object yang valid.');
            }
            $value = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($key === 'maintenance_mode') {
            $lowerVal = strtolower($value);
            $value = match (true) {
                in_array($lowerVal, ['1', 'true', 'yes', 'on', 'closed'], true) => 'true',
                $lowerVal === 'auto' => 'auto',
                default => 'false',
            };
        }

        Settings::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return redirect('/admin?tab=settings')->with('status', "Key '{$key}' berhasil disimpan.");
    }

    public function destroySettingKey(string $key): RedirectResponse
    {
        $defaultRow = collect(Settings::DEFAULT_SETTINGS)->firstWhere('key', $key);
        if ($defaultRow !== null) {
            Settings::query()->updateOrCreate(
                ['key' => $key],
                ['value' => (string) $defaultRow['value']]
            );

            return redirect('/admin?tab=settings')->with('status', "Key sistem '{$key}' dikembalikan ke nilai default.");
        }

        $row = Settings::query()->where('key', $key)->first();
        abort_unless($row, 404);
        $row->delete();

        return redirect('/admin?tab=settings')->with('status', "Key '{$key}' berhasil dihapus.");
    }

    /**
     * @return array{0: Collection<int, Product>, 1: Collection<int, Product>}
     */
    private function loadNormalizedCatalog(): array
    {
        $iconMap = collect(Product::DEFAULT_PRODUCTS)->pluck('image_url', 'id')->all();

        $normalizeProduct = function (Product $product) use ($iconMap): Product {
            if (empty($product->image_url) || str_ends_with($product->image_url, '.svg')) {
                $fallback = str_contains(mb_strtolower((string) $product->category), 'camilan')
                    ? '/img/cat-bakso.png'
                    : '/img/cat-dada.png';
                $product->image_url = $iconMap[$product->id] ?? $fallback;
            }

            return $product;
        };

        $products = Product::where('active', true)->orderBy('name')->get()->map($normalizeProduct);
        $archivedProducts = Product::where('active', false)->orderBy('name')->get()->map($normalizeProduct);

        return [$products, $archivedProducts];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, Product>  $archivedProducts
     * @param  Collection<int, Transaction>  $transactions
     * @return array<string, mixed>
     */
    private function buildOverviewStats(
        string $period,
        Collection $orders,
        Collection $products,
        Collection $archivedProducts,
        Collection $transactions
    ): array {
        $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        $revenue = $transactions->sum('total');
        $units = $transactions->sum(fn ($transaction) => collect(json_decode((string) $transaction->items, true) ?: [])->sum('quantity'));
        $todayTransactions = $transactions->filter(
            fn (Transaction $transaction): bool => Carbon::parse($transaction->created_at)->isToday()
        );

        $grouped = $transactions->groupBy(function ($transaction) use ($period) {
            $date = Carbon::parse($transaction->created_at);

            return match ($period) {
                'month' => $date->format('Y-m'),
                'week' => $date->format('o-\WW'),
                default => $date->format('Y-m-d'),
            };
        })->map(function ($rows, $key) use ($period, $months) {
            $date = Carbon::parse($rows->first()->created_at);
            $label = $period === 'month'
                ? $months[(int) $date->format('n')] . ' ' . $date->format('Y')
                : ($period === 'week' ? 'Mgg ' . $date->format('W') : $date->format('j') . ' ' . $months[(int) $date->format('n')]);

            return [
                'date' => $key,
                'label' => $label,
                'revenue' => $rows->sum('total'),
                'transactions' => $rows->count(),
            ];
        });

        $productsById = $products->concat($archivedProducts)->keyBy('id');
        $bestSellers = [];
        foreach ($transactions as $transaction) {
            foreach (json_decode((string) $transaction->items, true) ?: [] as $item) {
                $id = $item['product_id'] ?? '';
                if ($id === '') {
                    continue;
                }
                $catalogProduct = $productsById->get($id);
                $bestSellers[$id] ??= [
                    'product_id' => $id,
                    'name' => $item['name'] ?? ($catalogProduct?->name ?? 'Produk'),
                    'units' => 0,
                    'revenue' => 0,
                    'image_url' => $catalogProduct?->image_url ?? '/img/cat-dada.png',
                    'category' => $catalogProduct?->category ?? 'Ayam',
                ];
                $qty = (int) ($item['quantity'] ?? 0);
                $price = (int) ($item['price'] ?? 0);
                $bestSellers[$id]['units'] += $qty;
                $bestSellers[$id]['revenue'] += $price * $qty;
            }
        }
        usort($bestSellers, fn ($a, $b) => [$b['units'], $b['revenue']] <=> [$a['units'], $a['revenue']]);

        $transactionCount = $transactions->count();

        return [
            'revenue' => $revenue,
            'units' => $units,
            'transactions' => $transactionCount,
            'avg_order_value' => $transactionCount > 0 ? (int) round($revenue / $transactionCount) : 0,
            'chart' => $grouped->take(-($period === 'day' ? 14 : 12))->values(),
            'chart_max' => max(1, (int) $grouped->max('revenue')),
            'best_sellers' => array_slice($bestSellers, 0, 5),
            'today_revenue' => $todayTransactions->sum('total'),
            'today_transactions' => $todayTransactions->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'processing_orders' => $orders->whereIn('status', ['processing', 'ready'])->count(),
            'completed_orders' => $orders->whereIn('status', ['confirmed', 'completed'])->count(),
            'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            'low_stock' => $products->where('stock', '<=', 5)->count(),
            'out_of_stock' => $products->where('stock', '<=', 0)->count(),
            'active_products' => $products->count(),
        ];
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, Product>  $archivedProducts
     * @param  Collection<int, Transaction>  $transactions
     * @return array<string, mixed>
     */
    private function buildUmkmReport(
        Request $request,
        Collection $orders,
        Collection $products,
        Collection $archivedProducts,
        Collection $transactions
    ): array {
        $allowedRanges = ['today', '7d', '30d', 'month', 'all', 'custom'];
        $rawRange = (string) $request->query('range', '30d');
        $range = in_array($rawRange, $allowedRanges, true) ? $rawRange : '30d';

        $rawSource = (string) $request->query('source', 'all');
        $source = in_array($rawSource, ['all', 'online', 'manual'], true) ? $rawSource : 'all';

        $hasExplicitDates = $request->filled('start_date') || $request->filled('end_date');
        if ($hasExplicitDates && ! $request->has('range')) {
            $range = 'custom';
        }

        $now = now();
        $startAt = $now->copy()->subDays(29)->startOfDay();
        $endAt = $now->copy()->endOfDay();

        if ($range === 'custom' || ($range !== 'all' && $hasExplicitDates)) {
            $range = 'custom';
            try {
                if ($request->filled('start_date')) {
                    $startAt = Carbon::parse((string) $request->query('start_date'))->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endAt = Carbon::parse((string) $request->query('end_date'))->endOfDay();
                }
            } catch (\Throwable) {
                $range = '30d';
                $startAt = $now->copy()->subDays(29)->startOfDay();
                $endAt = $now->copy()->endOfDay();
            }
        } else {
            [$startAt, $endAt] = match ($range) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
                '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
                'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
                'all' => [$now->copy()->subYears(5)->startOfDay(), $now->copy()->endOfDay()],
                default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            };
        }

        if ($endAt->lt($startAt)) {
            [$startAt, $endAt] = [$endAt->copy()->startOfDay(), $startAt->copy()->endOfDay()];
        }

        $inPeriod = static function (mixed $createdAt) use ($range, $startAt, $endAt): bool {
            if ($range === 'all') {
                return true;
            }
            if (! $createdAt) {
                return false;
            }
            $dt = Carbon::parse($createdAt);

            return $dt->betweenIncluded($startAt, $endAt);
        };

        $filteredTransactions = $transactions
            ->filter(fn (Transaction $tx): bool => $inPeriod($tx->created_at) && ($source === 'all' || $tx->source === $source))
            ->sortByDesc('created_at')
            ->values();

        $filteredOrders = $orders
            ->filter(fn (Order $order): bool => $inPeriod($order->created_at))
            ->values();

        $totalRevenue = (int) $filteredTransactions->sum('total');
        $totalTransactions = $filteredTransactions->count();
        $totalUnits = 0;

        $onlineTxs = $filteredTransactions->where('source', 'online');
        $manualTxs = $filteredTransactions->where('source', 'manual');
        $onlineRevenue = (int) $onlineTxs->sum('total');
        $manualRevenue = (int) $manualTxs->sum('total');
        $onlineCount = $onlineTxs->count();
        $manualCount = $manualTxs->count();

        $onlineSharePct = $totalRevenue > 0 ? round(($onlineRevenue / $totalRevenue) * 100, 1) : 0.0;
        $manualSharePct = $totalRevenue > 0 ? round(($manualRevenue / $totalRevenue) * 100, 1) : 0.0;

        $confirmedOrders = $filteredOrders->filter(fn (Order $o): bool => in_array($o->status, ['confirmed', 'completed'], true));
        $pipelineOrders = $filteredOrders->filter(fn (Order $o): bool => in_array($o->status, ['pending', 'processing', 'ready'], true));

        $codOrders = $confirmedOrders->filter(fn (Order $o): bool => in_array($o->payment_method, ['cod', 'tunai'], true));
        $transferOrders = $confirmedOrders->filter(fn (Order $o): bool => ! in_array($o->payment_method, ['cod', 'tunai'], true));

        $paymentBreakdown = [
            'cod' => [
                'label' => 'Bayar Tunai (COD)',
                'orders' => $codOrders->count(),
                'revenue' => (int) $codOrders->sum('total'),
            ],
            'transfer' => [
                'label' => 'Transfer Bank / QRIS',
                'orders' => $transferOrders->count(),
                'revenue' => (int) $transferOrders->sum('total'),
            ],
        ];

        $conversionRate = $filteredOrders->count() > 0
            ? round(($confirmedOrders->count() / $filteredOrders->count()) * 100, 1)
            : 0.0;

        $productsById = $products->concat($archivedProducts)->keyBy('id');
        $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        $productStats = [];
        $dailyMap = [];
        $txRows = [];

        foreach ($filteredTransactions as $tx) {
            $decodedItems = json_decode((string) $tx->items, true) ?: [];
            $txUnits = 0;
            $itemParts = [];

            foreach ($decodedItems as $item) {
                $qty = max(0, (int) ($item['quantity'] ?? 0));
                $price = max(0, (int) ($item['price'] ?? 0));
                $pid = (string) ($item['product_id'] ?? '');
                $itemName = (string) ($item['name'] ?? 'Produk');

                $txUnits += $qty;
                $totalUnits += $qty;
                $itemParts[] = "{$qty}× {$itemName}";

                if ($pid !== '') {
                    $catalogProduct = $productsById->get($pid);
                    $productStats[$pid] ??= [
                        'id' => $pid,
                        'name' => $catalogProduct?->name ?? $itemName,
                        'category' => $catalogProduct?->category ?? 'Ayam',
                        'units' => 0,
                        'revenue' => 0,
                        'stock' => (int) ($catalogProduct?->stock ?? 0),
                    ];
                    $productStats[$pid]['units'] += $qty;
                    $productStats[$pid]['revenue'] += $price * $qty;
                }
            }

            $dt = Carbon::parse($tx->created_at ?? now());
            $dayKey = $dt->format('Y-m-d');
            $dayLabel = $dt->format('j') . ' ' . ($months[(int) $dt->format('n')] ?? $dt->format('M')) . ' ' . $dt->format('Y');

            $dailyMap[$dayKey] ??= [
                'date' => $dayKey,
                'label' => $dayLabel,
                'online_count' => 0,
                'manual_count' => 0,
                'units' => 0,
                'revenue' => 0,
            ];
            if ($tx->source === 'manual') {
                $dailyMap[$dayKey]['manual_count']++;
            } else {
                $dailyMap[$dayKey]['online_count']++;
            }
            $dailyMap[$dayKey]['units'] += $txUnits;
            $dailyMap[$dayKey]['revenue'] += (int) $tx->total;

            $txRows[] = [
                'id' => $tx->id,
                'display_id' => str_starts_with((string) $tx->id, 'order-') ? substr((string) $tx->id, 6) : substr((string) $tx->id, 0, 12),
                'time_label' => $dt->format('d M Y, H:i'),
                'source' => $tx->source,
                'customer_name' => $tx->customer_name,
                'items_summary' => implode(', ', $itemParts) ?: 'Transaksi Ayamo',
                'units' => $txUnits,
                'total' => (int) $tx->total,
            ];
        }

        $productBreakdown = collect($productStats)
            ->map(function (array $row) use ($totalRevenue): array {
                $row['share_pct'] = $totalRevenue > 0 ? round(($row['revenue'] / $totalRevenue) * 100, 1) : 0.0;

                return $row;
            })
            ->sortByDesc(fn (array $row) => [$row['revenue'], $row['units']])
            ->values()
            ->all();

        krsort($dailyMap);

        $firstTx = $transactions->first();
        $displayStart = $range === 'all' && $firstTx?->created_at
            ? Carbon::parse($firstTx->created_at)->toDateString()
            : $startAt->toDateString();

        return [
            'range' => $range,
            'source' => $source,
            'start_date' => $displayStart,
            'end_date' => $endAt->toDateString(),
            'label' => match ($range) {
                'today' => 'Hari Ini',
                '7d' => '7 Hari Terakhir',
                'month' => 'Bulan Ini',
                'all' => 'Semua Periode',
                'custom' => 'Periode Kustom',
                default => '30 Hari Terakhir',
            },
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'total_units' => $totalUnits,
            'avg_order_value' => $totalTransactions > 0 ? (int) round($totalRevenue / $totalTransactions) : 0,
            'online_revenue' => $onlineRevenue,
            'online_transactions' => $onlineCount,
            'manual_revenue' => $manualRevenue,
            'manual_transactions' => $manualCount,
            'online_share_pct' => $onlineSharePct,
            'manual_share_pct' => $manualSharePct,
            'payment_breakdown' => $paymentBreakdown,
            'pipeline_revenue' => (int) $pipelineOrders->sum('total'),
            'pipeline_orders' => $pipelineOrders->count(),
            'conversion_rate' => $conversionRate,
            'inventory_value' => (int) $products->sum(fn (Product $p): int => max(0, (int) $p->price) * max(0, (int) $p->stock)),
            'inventory_units' => (int) $products->sum(fn (Product $p): int => max(0, (int) $p->stock)),
            'product_breakdown' => $productBreakdown,
            'daily_rows' => array_values($dailyMap),
            'transactions' => $txRows,
        ];
    }
}
