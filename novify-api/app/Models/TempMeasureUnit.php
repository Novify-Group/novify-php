<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempMeasureUnit extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the merchant-specific measure units based on this template
     */
    public function merchantUnits(): HasMany
    {
        return $this->hasMany(ProductMeasureUnit::class, 'temp_measure_unit_id');
    }

    /**
     * Scope a query to only include active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 