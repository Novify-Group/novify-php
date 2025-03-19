<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'tran_reference',
        'from_wallet_id',
        'to_wallet_id',
        'currency_id',
        'amount',
        'type',
        'payment_method',
        'payment_method_description',
        'tran_status',
        'narration',
        'tran_receipt',
        'tran_date',
        'net_impact',
        'external_customer_number'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'net_impact' => 'decimal:2',
        'tran_date' => 'datetime',
    ];

    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function fromMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'from_merchant_id');
    }

    public function toMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'to_merchant_id');
    }
} 