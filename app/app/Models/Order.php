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
}
