<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    public const CREATED_AT = null;

    public const DEFAULT_SETTINGS = [
        ['key' => 'store_name', 'value' => 'AYAMO'],
        ['key' => 'store_tagline', 'value' => 'Eat, Chill, Repeat!'],
        ['key' => 'store_time', 'value' => '15.00 - 03.00'],
        ['key' => 'admin_key', 'value' => 'kelompok17app'],
        ['key' => 'maintenance_mode', 'value' => 'false'],
        ['key' => 'phone_number', 'value' => '628994913542'],
        ['key' => 'address', 'value' => 'Alamat toko Ayamo Jl.prof.dr.sharif thayib-gp teungoh. Kec. Langsa kota.kota langsa'],
        [
            'key' => 'payment_channel',
            'value' => '{
                "qris": {
                    "enable": true,
                    "type": "image",
                    "name": "QRIS",
                    "image": ""
                },
                "bsi": {
                    "enable": true,
                    "type": "manual",
                    "name": "Bank Syariah Indonesia",
                    "number": "1234 5678 90",
                    "holder": "Ayamo"
                },
                "whatsapp": {
                    "enable": true,
                    "type": "button",
                    "name": "WhatsApp",
                    "url": "https://wa.me/62895346200506",
                    "message": "Halo, saya ingin memesan Ayamo!"
                }
            }',
        ],
    ];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function protectedKeys(): array
    {
        return array_values(array_map(
            static fn(array $row): string => (string) $row['key'],
            self::DEFAULT_SETTINGS
        ));
    }

    public static function seedDefaults(): void
    {
        try {
            $existingKeys = static::query()->pluck('key')->all();
            $existingLookup = array_flip($existingKeys);

            foreach (self::DEFAULT_SETTINGS as $data) {
                if (isset($existingLookup[$data['key']])) {
                    continue;
                }

                static::firstOrCreate(
                    ['key' => $data['key']],
                    ['value' => $data['value']]
                );
            }
        } catch (\Throwable) {
            // Fallback gracefully if settings table is not migrated yet
        }
    }

    public static function getMap(): array
    {
        $defaults = collect(self::DEFAULT_SETTINGS)->pluck('value', 'key')->all();

        try {
            $dbSettings = static::query()->pluck('value', 'key')->all();
            return array_merge($defaults, $dbSettings);
        } catch (\Throwable) {
            return $defaults;
        }
    }

    /**
     * Parse and normalize `payment_channel` JSON with safe defaults and backward-compatible bank sync.
     *
     * @param  array<string, string>|null  $settingsMap
     * @return array{
     *     qris: array{enable: bool, type: string, name: string, image: string},
     *     transfers: array<int, array{key: string, enable: bool, type: string, name: string, number: string, holder: string}>,
     *     enabled_transfers: array<int, array{key: string, enable: bool, type: string, name: string, number: string, holder: string}>,
     *     whatsapp: array{enable: bool, type: string, name: string, url: string, message: string}
     * }
     */
    public static function getPaymentChannels(?array $settingsMap = null): array
    {
        $map = $settingsMap ?? static::getMap();
        $defaultJson = (string) (collect(self::DEFAULT_SETTINGS)->firstWhere('key', 'payment_channel')['value'] ?? '{}');
        $fallbackRaw = json_decode($defaultJson, true);
        $fallbackRaw = is_array($fallbackRaw) ? $fallbackRaw : [];

        $decoded = json_decode((string) ($map['payment_channel'] ?? ''), true);
        $raw = (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && $decoded !== [])
            ? $decoded
            : $fallbackRaw;

        $qrisRaw = is_array($raw['qris'] ?? null) ? $raw['qris'] : ($fallbackRaw['qris'] ?? []);
        $qris = [
            'enable' => (bool) ($qrisRaw['enable'] ?? true),
            'type' => 'image',
            'name' => trim((string) ($qrisRaw['name'] ?? 'QRIS')) ?: 'QRIS',
            'image' => trim((string) ($qrisRaw['image'] ?? '')),
        ];

        $transfers = [];
        foreach ($raw as $key => $entry) {
            if (in_array((string) $key, ['qris', 'whatsapp'], true) || !is_array($entry)) {
                continue;
            }
            if (($entry['type'] ?? 'manual') !== 'manual' && !isset($entry['number'])) {
                continue;
            }

            $transfers[] = [
                'key' => (string) $key,
                'enable' => (bool) ($entry['enable'] ?? true),
                'type' => 'manual',
                'name' => trim((string) ($entry['name'] ?? strtoupper((string) $key))),
                'number' => trim((string) ($entry['number'] ?? '')),
                'holder' => trim((string) ($entry['holder'] ?? ($map['bank_account_holder'] ?? 'Ayamo'))),
            ];
        }

        if ($transfers === []) {
            $transfers[] = [
                'key' => 'bsi',
                'enable' => true,
                'type' => 'manual',
                'name' => trim((string) ($map['bank_name'] ?? 'Bank Syariah Indonesia')),
                'number' => trim((string) ($map['bank_account_number'] ?? '1234 5678 90')),
                'holder' => trim((string) ($map['bank_account_holder'] ?? 'Ayamo')),
            ];
        } elseif (count($transfers) === 1 && $transfers[0]['key'] === 'bsi') {
            $isInitialBsi = $transfers[0]['name'] === 'Bank Syariah Indonesia'
                && $transfers[0]['number'] === '1234 5678 90'
                && $transfers[0]['holder'] === 'Ayamo';
            if ($isInitialBsi) {
                $transfers[0]['name'] = trim((string) ($map['bank_name'] ?? $transfers[0]['name']));
                $transfers[0]['number'] = trim((string) ($map['bank_account_number'] ?? $transfers[0]['number']));
                $transfers[0]['holder'] = trim((string) ($map['bank_account_holder'] ?? $transfers[0]['holder']));
            }
        }

        $enabledTransfers = array_values(array_filter(
            $transfers,
            static fn(array $ch): bool => $ch['enable'] && $ch['name'] !== '' && $ch['number'] !== ''
        ));

        $waDigits = preg_replace('/[^0-9]/', '', (string) ($map['phone_number'] ?? '62895346200506'));
        if ($waDigits !== '' && str_starts_with($waDigits, '0')) {
            $waDigits = '62' . substr($waDigits, 1);
        }

        $waRaw = is_array($raw['whatsapp'] ?? null) ? $raw['whatsapp'] : ($fallbackRaw['whatsapp'] ?? []);
        $whatsapp = [
            'enable' => (bool) ($waRaw['enable'] ?? true),
            'type' => 'button',
            'name' => trim((string) ($waRaw['name'] ?? 'WhatsApp')) ?: 'WhatsApp',
            'url' => trim((string) ($waRaw['url'] ?? '')) ?: ('https://wa.me/' . $waDigits),
            'message' => trim((string) ($waRaw['message'] ?? 'Halo, saya ingin memesan Ayamo!')),
        ];

        return [
            'qris' => $qris,
            'transfers' => $transfers,
            'enabled_transfers' => $enabledTransfers,
            'whatsapp' => $whatsapp,
        ];
    }

    /**
     * Evaluate current store open/closed status supporting manual override (`maintenance_mode`)
     * and schedule-based checks (`store_time`, Asia/Jakarta).
     *
     * @param  array<string, string>|null  $settingsMap
     * @return array{is_open: bool, mode: string, label: string, badge: string, reason: string, store_time: string}
     */
    public static function getStoreStatus(?array $settingsMap = null, ?\Carbon\CarbonInterface $now = null): array
    {
        $map = $settingsMap ?? self::getMap();
        $storeTime = trim((string) ($map['store_time'] ?? '15.00 - 03.00')) ?: '15.00 - 03.00';
        $rawMode = strtolower(trim((string) ($map['maintenance_mode'] ?? 'false')));
        $reason = trim((string) ($map['store_closed_reason'] ?? ''));

        $mode = match (true) {
            in_array($rawMode, ['true', '1', 'closed', 'yes', 'on'], true) => 'closed',
            $rawMode === 'auto' => 'auto',
            default => 'open',
        };

        if ($mode === 'closed') {
            $fallbackReason = $reason !== '' ? $reason : 'Toko sedang tutup sementara. Silakan kembali lagi nanti.';

            return [
                'is_open' => false,
                'mode' => 'closed',
                'label' => 'Toko Tutup',
                'badge' => 'Tutup Hari Ini',
                'reason' => $fallbackReason,
                'store_time' => $storeTime,
            ];
        }

        if ($mode === 'open') {
            return [
                'is_open' => true,
                'mode' => 'open',
                'label' => 'Toko Buka',
                'badge' => 'Buka ' . $storeTime,
                'reason' => '',
                'store_time' => $storeTime,
            ];
        }

        // Mode = 'auto': evaluate operating hours in Asia/Jakarta (handles overnight ranges e.g. 15.00 - 03.00)
        $current = $now ? $now->copy()->setTimezone('Asia/Jakarta') : now('Asia/Jakarta');
        $isWithinHours = true;

        if (preg_match('/(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})/', $storeTime, $m)) {
            $startMin = ((int) $m[1] * 60) + (int) $m[2];
            $endMin = ((int) $m[3] * 60) + (int) $m[4];
            $nowMin = ((int) $current->format('H') * 60) + (int) $current->format('i');

            if ($startMin <= $endMin) {
                $isWithinHours = $nowMin >= $startMin && $nowMin < $endMin;
            } else {
                // Overnight schedule (e.g. 15:00 -> 03:00)
                $isWithinHours = $nowMin >= $startMin || $nowMin < $endMin;
            }
        }

        if (!$isWithinHours) {
            return [
                'is_open' => false,
                'mode' => 'auto',
                'label' => 'Di Luar Jam Buka',
                'badge' => 'Tutup (' . $storeTime . ')',
                'reason' => $reason !== '' ? $reason : "Toko beroperasi pukul {$storeTime} WIB.",
                'store_time' => $storeTime,
            ];
        }

        return [
            'is_open' => true,
            'mode' => 'auto',
            'label' => 'Toko Buka (Jadwal)',
            'badge' => 'Buka ' . $storeTime,
            'reason' => '',
            'store_time' => $storeTime,
        ];
    }
}
