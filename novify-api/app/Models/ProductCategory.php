<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    protected $fillable = [
        'merchant_id',
        'temp_category_id',
        'name',
        'image',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tempCategory(): BelongsTo
    {
        return $this->belongsTo(TempCategory::class, 'temp_category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
} 