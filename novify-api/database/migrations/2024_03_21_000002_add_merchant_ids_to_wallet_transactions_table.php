<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreignId('from_merchant_id')->nullable()->constrained('merchants');
            $table->foreignId('to_merchant_id')->nullable()->constrained('merchants');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['from_merchant_id']);
            $table->dropForeign(['to_merchant_id']);
            $table->dropColumn(['from_merchant_id', 'to_merchant_id']);
        });
    }
}; 