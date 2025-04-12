<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('biller_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biller_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('min_amount', 15, 2)->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->boolean('is_amount_fixed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['biller_id', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('biller_items');
    }
}; 