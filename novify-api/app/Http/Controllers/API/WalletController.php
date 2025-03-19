<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Wallet\WalletTopupRequest;
use App\Http\Requests\Wallet\WalletTransferRequest;
use App\Services\WalletService;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\Wallet\WalletPayRequest;
use App\Http\Requests\Wallet\WalletCreateRequest;

class WalletController extends BaseApiController
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function topup(WalletTopupRequest $request)
    { 
        return $this->execute(function () use ($request) {
            return $this->walletService->topup($request->user(), $request->validated());
        });
    }

    public function transfer(WalletTransferRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->transfer($request->user(), $request->validated());
        });
    }

    public function pay(WalletPayRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->pay($request->user(), $request->validated());
        });
    }

    public function balance(Wallet $wallet)
    {
        return $this->execute(function () use ($wallet) {
            return $this->walletService->getBalance($wallet);
        });
    }

    public function getTransactions(Request $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->getTransactions($request->all());
        });
    }

    
    public function getTransaction(WalletTransaction $transaction)
    { 
        return $this->execute(function () use ($transaction) {
            return $this->walletService->getTransaction($transaction);
        });
    }

    public function create(WalletCreateRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->createWallet($request->user(), $request->validated());
        });
    }

    public function getWallets(Request $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->listWallets($request->user(), $request->per_page ?? 20);
        });
    }

    public function getWallet(Wallet $wallet)       
    {
        return $this->execute(function () use ($wallet) {
            return $this->walletService->getWallet($wallet);
        });
    }
    

} 