<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'merchant_id',
        'customer_merchant_id',
        'name',
        'phone_number',
        'email',
        'address',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customerMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'customer_merchant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
} 