<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'parent_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the merchant-specific categories based on this template
     */
    public function merchantCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'temp_category_id');
    }

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(TempCategory::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(TempCategory::class, 'parent_id');
    }

    /**
     * Scope a query to only include active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
} 