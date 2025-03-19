<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tran_reference')->unique();
            $table->foreignId('from_wallet_id')->nullable()->constrained('wallets');
            $table->foreignId('to_wallet_id')->nullable()->constrained('wallets');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['TOPUP', 'CASHOUT', 'DEBIT', 'FEES', 'SALE', 'TRANSFER', 'PAYMENT']);
            $table->enum('payment_method', ['WALLET', 'CARD', 'MOBILEMONEY', 'CASH', 'BANK', 'OTHER']);
            $table->string('payment_method_description')->nullable();
            $table->enum('tran_status', ['PENDING', 'FAILED', 'SUCCESSFUL']);
            $table->text('narration');
            $table->string('tran_receipt')->nullable();
            $table->string('external_customer_number')->nullable();
            $table->timestamp('tran_date');
            $table->decimal('net_impact', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
}; 