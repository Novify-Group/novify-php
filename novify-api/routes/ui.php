<?php

use App\Http\Controllers\UI\DashboardController;
use App\Http\Controllers\UI\BranchController;
use App\Http\Controllers\UI\ProductController;
use App\Http\Controllers\UI\OrderController;
use App\Http\Controllers\UI\CustomerController;
use App\Http\Controllers\UI\UserController;
use App\Http\Controllers\UI\WalletController;
use App\Http\Controllers\UI\BillController;
use Illuminate\Support\Facades\Route;

// UI Routes - Web Interface
Route::prefix('ui')->name('ui.')->middleware(['web', 'auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Branches
    Route::resource('branches', BranchController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Orders
    Route::resource('orders', OrderController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Users (Merchant Users)
    Route::resource('users', UserController::class);
    
    // Wallet
    Route::resource('wallet', WalletController::class);
    
    // Bills
    Route::resource('bills', BillController::class);
    
    // Additional routes for specific functionality
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('categories', [ProductController::class, 'categories'])->name('categories');
        Route::get('measure-units', [ProductController::class, 'measureUnits'])->name('measure-units');
        Route::post('{product}/images', [ProductController::class, 'storeImage'])->name('store-image');
        Route::put('{product}/stock', [ProductController::class, 'updateStock'])->name('update-stock');
    });
    
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::post('topup', [WalletController::class, 'topup'])->name('topup');
        Route::post('transfer', [WalletController::class, 'transfer'])->name('transfer');
        Route::get('transactions', [WalletController::class, 'transactions'])->name('transactions');
    });
    
    // Additional bill routes
    Route::prefix('bills')->name('bills.')->group(function () {
        Route::get('categories', [BillController::class, 'categories'])->name('categories');
        Route::get('categories/{category}/billers', [BillController::class, 'showBillers'])->name('show-billers');
    });
});
