<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const DEFAULT_PRODUCTS = [
        ['id' => 'dada-ayam', 'name' => 'Dada Ayam', 'category' => 'Ayam', 'price' => 7000, 'stock' => 50, 'description' => 'Dada ayam crispy, tebal dan juicy', 'image_url' => '/img/cat-dada.png'],
        ['id' => 'sayap-ayam', 'name' => 'Sayap Ayam', 'category' => 'Ayam', 'price' => 7000, 'stock' => 45, 'description' => 'Sayap renyah, enak buat ngemil', 'image_url' => '/img/cat-sayap.png'],
        ['id' => 'paha-atas', 'name' => 'Paha Atas', 'category' => 'Ayam', 'price' => 7000, 'stock' => 45, 'description' => 'Paha atas, dagingnya tebal', 'image_url' => '/img/cat-paha.png'],
        ['id' => 'paha-bawah', 'name' => 'Paha Bawah', 'category' => 'Ayam', 'price' => 7000, 'stock' => 45, 'description' => 'Paha bawah, lebih gampang dimakan', 'image_url' => '/img/cat-paha.png'],
        ['id' => 'ceker-ayam', 'name' => 'Ceker Ayam', 'category' => 'Ayam', 'price' => 500, 'stock' => 100, 'description' => 'Ceker empuk dengan bumbu gurih', 'image_url' => '/img/cat-ceker.png'],
        ['id' => 'hati-ayam', 'name' => 'Hati Ayam', 'category' => 'Ayam', 'price' => 2000, 'stock' => 40, 'description' => 'Hati ayam gurih dan lembut', 'image_url' => '/img/cat-paha.png'],
        ['id' => 'leher-ayam', 'name' => 'Leher Ayam', 'category' => 'Ayam', 'price' => 5000, 'stock' => 35, 'description' => 'Leher ayam crispy gurih', 'image_url' => '/img/cat-leher.png'],
        ['id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'category' => 'Camilan', 'price' => 1000, 'stock' => 60, 'description' => 'Bakso goreng renyah dan gurih', 'image_url' => '/img/cat-bakso.png'],
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
        try {
            foreach (self::DEFAULT_PRODUCTS as $data) {
                static::firstOrCreate(
                    ['id' => $data['id']],
                    array_merge($data, ['active' => true])
                );
            }
        } catch (\Throwable) {
            // Fallback gracefully if DB is unreachable
        }
    }
}
