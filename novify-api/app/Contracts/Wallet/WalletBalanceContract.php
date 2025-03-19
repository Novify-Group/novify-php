<?php

namespace App\Contracts\Wallet;

use App\Models\Wallet;

interface WalletBalanceContract
{
    public function getBalance(Wallet $wallet): array;
    public function getCardDetails(Wallet $wallet): array;
    public function debitWallet(Wallet $wallet, float $amount): void;
    public function creditWallet(Wallet $wallet, float $amount): void;
}