<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->boolean('is_main_branch')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['merchant_id', 'name']);
            $table->unique(['merchant_id', 'phone_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}; 