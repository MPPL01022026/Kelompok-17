<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'source',
        'customer_name',
        'items',
        'total',
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
}
