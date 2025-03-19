<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('dob');
            $table->string('id_type');
            $table->string('id_number');
            $table->string('id_picture_path');
            $table->string('passport_photo_path');
            $table->string('phone_number')->unique();
            $table->string('email')->unique();
            $table->string('password');
            
            // Store details
            $table->string('store_name')->unique();
            $table->string('store_logo_path')->nullable();
            $table->text('store_description')->nullable();
            
            // Additional business fields
            $table->string('licence_number')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->boolean('is_licenced')->default(false);
            $table->date('date_started')->nullable();
            $table->foreignId('market_area_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}; 