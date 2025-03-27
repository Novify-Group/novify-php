<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
        'sku',
        'description',
        'cost_price',
        'selling_price',
        'product_category_id',
        'product_measure_unit_id',
        'supplier_id',
        'featured_image',
        'is_featured',
        'is_discounted',
        'discount_percentage',
        'discount_amount',
        'is_discount_percentage',
        'discounted_price',
        'is_taxable',
        'tax_percentage',
        'tax_amount',
        'is_tax_percentage',
        'is_tax_amount',
        'is_inventory_tracked',
        'stock_quantity',
        'min_stock_level',
        'is_inventory_low',
        'is_inventory_out',
        'expiry_date'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_discounted' => 'boolean',
        'is_discount_percentage' => 'boolean',
        'is_taxable' => 'boolean',
        'is_tax_percentage' => 'boolean',
        'is_tax_amount' => 'boolean',
        'is_inventory_tracked' => 'boolean',
        'is_inventory_low' => 'boolean',
        'is_inventory_out' => 'boolean',
        'expiry_date' => 'date'
    ];

    protected function getFeaturedImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return config('app.url') . '/' . $value;
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function measure_unit(): BelongsTo
    {
        return $this->belongsTo(ProductMeasureUnit::class, 'product_measure_unit_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
} 