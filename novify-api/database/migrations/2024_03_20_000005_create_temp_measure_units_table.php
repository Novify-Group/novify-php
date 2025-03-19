<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temp_measure_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol', 10)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'symbol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_measure_units');
    }
}; 