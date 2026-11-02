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
    ];

    protected $casts = [
        'total' => 'integer',
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

    public static function generateCode(): string
    {
        for ($i = 0; $i < 20; $i++) {
            $code = 'AYM-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            try {
                if (!static::where('id', $code)->exists()) {
                    return $code;
                }
            } catch (\Throwable) {
                return $code;
            }
        }

        return 'AYM-' . random_int(1000, 9999);
    }
}
