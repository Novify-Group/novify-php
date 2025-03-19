<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
        'phone_number',
        'email',
        'address',
        'city',
        'is_main_branch',
        'is_active'
    ];

    protected $casts = [
        'is_main_branch' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(MerchantUser::class);
    }
} 