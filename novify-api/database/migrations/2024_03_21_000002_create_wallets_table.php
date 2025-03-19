<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Main Wallet');
            $table->string('wallet_number')->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->foreignId('currency_id')->constrained();
            $table->string('currency_code', 3);
            $table->enum('type', ['MAIN', 'SAVINGS', 'EXPENSES', 'INVESTMENT', 'OTHER'])->default('MAIN');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}; 