<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMeasureUnit extends Model
{
    protected $fillable = [
        'merchant_id',
        'temp_measure_unit_id',
        'name',
        'symbol',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tempMeasureUnit(): BelongsTo
    {
        return $this->belongsTo(TempMeasureUnit::class, 'temp_measure_unit_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'measure_unit_id');
    }
} 