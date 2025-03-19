<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:product_categories,id',
            'measure_unit_id' => 'required|exists:product_measure_units,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'featured_image' => 'nullable|string', // base64
            'is_featured' => 'boolean',
            'is_discounted' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|required_if:is_discounted,true',
            'discount_amount' => 'nullable|numeric|min:0|required_if:is_discounted,true',
            'is_discount_percentage' => 'boolean',
            'is_taxable' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100|required_if:is_taxable,true',
            'tax_amount' => 'nullable|numeric|min:0|required_if:is_taxable,true',
            'is_tax_percentage' => 'boolean',
            'is_inventory_tracked' => 'boolean',
            'expiry_date' => 'nullable|date|after:today',
            
            // For variants
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.sku' => 'required|string|unique:product_variants,sku',
            'variants.*.cost_price' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0|gte:variants.*.cost_price',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.min_stock_level' => 'required|integer|min:0',
            'variants.*.attributes' => 'required|array',
            
            // For images
            'images' => 'nullable|array',
            'images.*.image_url' => 'required|string', // base64
            'images.*.is_featured' => 'boolean',
            'images.*.sort_order' => 'integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'selling_price.gte' => 'The selling price must be greater than or equal to the cost price.',
            'variants.*.selling_price.gte' => 'The variant selling price must be greater than or equal to its cost price.',
            'sku.unique' => 'This SKU has already been taken.',
            'variants.*.sku.unique' => 'This variant SKU has already been taken.',
            'discount_percentage.required_if' => 'The discount percentage is required when the product is discounted.',
            'tax_percentage.required_if' => 'The tax percentage is required when the product is taxable.',
        ];
    }
} 