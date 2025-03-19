<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Wallet extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
        'wallet_number',
        'balance',
        'currency_id',
        'currency_code',
        'type',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
} 