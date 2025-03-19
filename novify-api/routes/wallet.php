<?php

use App\Http\Controllers\API\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')
    ->prefix('wallet')
    ->group(function () {
        Route::post('/topup', [WalletController::class, 'topup']);
        Route::post('/pay', [WalletController::class, 'pay']);
        Route::post('/transfer', [WalletController::class, 'transfer']);
        Route::get('/transactions', [WalletController::class, 'getTransactions']);
        Route::get('/transactions/{transaction}', [WalletController::class, 'show']);
        Route::post('/', [WalletController::class, 'create']);
        Route::get('/', [WalletController::class, 'getWallets']);
        Route::get('/{wallet}', [WalletController::class, 'getWallet']);
    });

