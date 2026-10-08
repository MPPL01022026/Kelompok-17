<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $primaryKey = 'identifier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'identifier',
        'attempts',
        'locked_until',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'locked_until' => 'datetime',
    ];
}
