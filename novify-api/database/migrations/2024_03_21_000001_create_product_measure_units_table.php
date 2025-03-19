<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_measure_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('temp_measure_unit_id')->nullable()->constrained('temp_measure_units');
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['merchant_id', 'temp_measure_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_measure_units');
    }
}; 