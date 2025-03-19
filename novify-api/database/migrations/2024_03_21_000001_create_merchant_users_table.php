<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('merchant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('phone_number')->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('photo_path')->nullable();
            $table->string('id_picture_path')->nullable();
            $table->enum('role', ['ATTENDANT', 'DISTRIBUTOR']);
            $table->boolean('is_active')->default(true);
            $table->boolean('force_password_change')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_users');
    }
}; 