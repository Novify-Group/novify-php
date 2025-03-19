<?php

namespace App\Services\Wallet;

use App\Contracts\Wallet\WalletBalanceContract;
use App\Models\Wallet;
class InternalWalletBalanceService implements WalletBalanceContract
{
    public function getBalance(Wallet $wallet): array
    {
        return [
            'balance' => $wallet->balance,
            'currency_code' => $wallet->currency_code,
            'wallet_number' => $wallet->wallet_number,
            'name' => $wallet->name,
            'id' => $wallet->id
        ];
    }

    public function getCardDetails(Wallet $wallet): array
    {
        return [];
    }

    public function debitWallet(Wallet $wallet, float $amount): void
    {
        $wallet->balance -= $amount;
        $wallet->save();
    }

    public function creditWallet(Wallet $wallet, float $amount): void
    {
        $wallet->balance += $amount;
        $wallet->save();
    }
    
}
