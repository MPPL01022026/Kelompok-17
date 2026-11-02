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
    public $timestamps = false;

    public const DEFAULT_SETTINGS = [
        ['key' => 'store_name', 'value' => 'AYAMO'],
        ['key' => 'store_tagline', 'value' => 'Eat, Chill, Repeat!'],
        ['key' => 'store_time', 'value' => '15.00 - 03.00'],
        ['key' => 'admin_key', 'value' => 'kelompok17app'],
        ['key' => 'maintenance_mode', 'value' => 'false'],
        ['key' => 'phone_number', 'value' => '62895346200506'],
        ['key' => 'address', 'value' => 'Alamat toko Ayamo Jl.prof.dr.sharif thayib-gp teungoh. Kec. Langsa kota.kota langsa'],
        ['key' => 'bank_name', 'value' => 'Bank Syariah Indonesia'],
        ['key' => 'bank_account_number', 'value' => '1234 5678 90'],
        ['key' => 'bank_account_holder', 'value' => 'Ayamo'],
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

    public static function seedDefaults(): void
    {
        try {
            foreach (self::DEFAULT_SETTINGS as $data) {
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
}
