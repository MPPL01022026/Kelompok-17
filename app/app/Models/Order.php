<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_name',
        'phone',
        'items',
        'total',
        'note',
        'payment_method',
        'status',
        'stock_reserved',
    ];

    protected $casts = [
        'total' => 'integer',
        'stock_reserved' => 'boolean',
    ];

    public function getParsedItemsAttribute(): array
    {
        if (is_array($this->items)) {
            return $this->items;
        }
        return json_decode($this->items, true) ?: [];
    }

    public function getCodeAttribute(): string
    {
        if (str_starts_with((string) $this->id, 'AYM-')) {
            return (string) $this->id;
        }

        $num = str_pad((string) (abs(crc32((string) $this->id)) % 10000), 4, '0', STR_PAD_LEFT);
        return 'AYM-' . $num;
    }

    public function getItemsSummaryAttribute(): string
    {
        return collect($this->parsed_items)
            ->map(fn (array $item): string => ($item['quantity'] ?? 1) . 'x ' . ($item['name'] ?? 'Item'))
            ->join(', ');
    }

    /**
     * In-process deduplication cache for unsaved order codes generated in the same runtime.
     *
     * @var array<string, true>
     */
    private static array $recentCodes = [];

    /**
     * Generate a collision-resistant, unambiguous order code formatted as `AYM-YYMMDD-XXXX`
     * using Crockford-like Base32 (`30^4 = 810,000` codes/day; automatically escalates to 6 chars).
     *
     * @param  array<int, string>  $excludeCodes
     */
    public static function generateCode(array $excludeCodes = []): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxIndex = strlen($alphabet) - 1;
        $datePart = now('Asia/Jakarta')->format('ymd');
        $excludeLookup = $excludeCodes !== [] ? array_flip($excludeCodes) : [];

        $recordCode = static function (string $code): string {
            if (count(self::$recentCodes) >= 1024) {
                self::$recentCodes = array_slice(self::$recentCodes, -512, null, true);
            }
            self::$recentCodes[$code] = true;

            return $code;
        };

        for ($attempt = 0; $attempt < 24; $attempt++) {
            $length = $attempt >= 12 ? 6 : 4;
            $suffix = '';

            for ($pos = 0; $pos < $length; $pos++) {
                $suffix .= $alphabet[random_int(0, $maxIndex)];
            }

            $code = "AYM-{$datePart}-{$suffix}";
            if (isset($excludeLookup[$code]) || isset(self::$recentCodes[$code])) {
                continue;
            }

            try {
                if (! static::where('id', $code)->exists()) {
                    return $recordCode($code);
                }
            } catch (\Throwable) {
                return $recordCode($code);
            }
        }

        // Deterministic microsecond-entropy fallback if DB collisions ever exhausted random retries
        $entropy = strtoupper(substr(hash('sha256', microtime(true) . bin2hex(random_bytes(8))), 0, 8));

        return $recordCode("AYM-{$datePart}-{$entropy}");
    }
}
