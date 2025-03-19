<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'currency_code',
        'currency_symbol',
        'flag_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
} 