<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const DEFAULT_PRODUCTS = [
        ['id' => 'combo-original', 'name' => 'Ayam Crispy Original', 'category' => 'Ayam Goreng', 'price' => 15000, 'stock' => 40, 'description' => 'Ayam goreng crispy bumbu original, renyah di luar juicy di dalam.', 'image_url' => '/img/combo-original.jpg'],
        ['id' => 'combo-pedas', 'name' => 'Ayam Crispy Pedas', 'category' => 'Ayam Goreng', 'price' => 16000, 'stock' => 35, 'description' => 'Sensasi pedas gurih dengan racikan cabai pilihan Ayamo.', 'image_url' => '/img/combo-pedas.jpg'],
        ['id' => 'paket-nasi', 'name' => 'Paket Nasi + Ayam', 'category' => 'Paket Hemat', 'price' => 22000, 'stock' => 30, 'description' => '1 potong ayam crispy, nasi hangat, sambal, dan lalapan segar.', 'image_url' => '/img/paket-nasi.jpg'],
        ['id' => 'paket-combo', 'name' => 'Combo Nasi + Ayam + Es Teh', 'category' => 'Paket Hemat', 'price' => 26000, 'stock' => 28, 'description' => 'Paket lengkap: nasi, ayam crispy, sambal, dan es teh manis.', 'image_url' => '/img/paket-combo.jpg'],
        ['id' => 'sayap-crispy', 'name' => 'Sayap Crispy (3 pcs)', 'category' => 'Ayam Goreng', 'price' => 18000, 'stock' => 25, 'description' => 'Tiga potong sayap crispy renyah, cocok untuk cemilan.', 'image_url' => '/img/sayap-crispy.jpg'],
        ['id' => 'paket-keluarga', 'name' => 'Paket Keluarga (5 pcs + 3 Nasi)', 'category' => 'Paket Keluarga', 'price' => 89000, 'stock' => 15, 'description' => 'Lima potong ayam crispy, tiga porsi nasi, dan sambal spesial.', 'image_url' => '/img/paket-keluarga.jpg'],
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category',
        'price',
        'stock',
        'description',
        'image_url',
        'active',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'active' => 'boolean',
    ];

    public static function seedDefaults(): void
    {
        foreach (self::DEFAULT_PRODUCTS as $data) {
            static::firstOrCreate(
                ['id' => $data['id']],
                array_merge($data, ['active' => true])
            );
        }
    }
}
