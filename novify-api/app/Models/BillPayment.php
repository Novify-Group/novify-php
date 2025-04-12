<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillPayment extends Model
{
    protected $fillable = [
        'biller_item_id',
        'wallet_id',
        'payment_method_id',
        'bill_code',
        'amount',
        'status',
        'reference',
        'provider_reference',
        'validation_data',
        'payment_data',
        'meta_data'
    ];

    protected $casts = [
        'validation_data' => 'array',
        'payment_data' => 'array',
        'meta_data' => 'array'
    ];

    public function billerItem(): BelongsTo
    {
        return $this->belongsTo(BillerItem::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
} 