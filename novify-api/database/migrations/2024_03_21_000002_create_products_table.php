<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('category_id')->constrained('product_categories');
            $table->foreignId('measure_unit_id')->constrained('product_measure_units');
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->foreignId('product_category_id')->constrained('product_categories');
            $table->foreignId('product_measure_unit_id')->constrained('product_measure_units');
            $table->foreignId('supplier_id')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_discounted')->default(false);
            $table->decimal('discount_percentage', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->boolean('is_discount_percentage')->default(false);
            $table->boolean('discounted_price')->default(false);
            $table->boolean('is_taxable')->default(false);
            $table->decimal('tax_percentage', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->boolean('is_tax_percentage')->default(false);
            $table->boolean('is_tax_amount')->default(false);
            $table->boolean('is_inventory_tracked')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->boolean('is_inventory_low')->default(false);
            $table->boolean('is_inventory_out')->default(false);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_expiry_tracked')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 