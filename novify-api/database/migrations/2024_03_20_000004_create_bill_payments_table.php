<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biller_item_id');
            $table->foreignId('wallet_id')->nullable();
            $table->foreignId('merchant_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('payment_method')->default('WALLET');
            $table->string('bill_code');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING');
            $table->string('reference')->unique();
            $table->string('provider_reference')->nullable();
            $table->json('validation_data')->nullable();
            $table->json('payment_data')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_payments');
    }
}; 