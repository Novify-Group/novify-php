<?php

use App\Http\Controllers\UI\AdminController;
use Illuminate\Support\Facades\Route;

// Admin Routes - System Administration
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'admin'])->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Merchant Management
    Route::prefix('merchants')->name('merchants.')->group(function () {
        Route::get('/', [AdminController::class, 'merchants'])->name('index');
        Route::get('/{merchant}', [AdminController::class, 'showMerchant'])->name('show');
        Route::put('/{merchant}/status', [AdminController::class, 'updateMerchantStatus'])->name('update-status');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminController::class, 'reports'])->name('index');
        Route::post('/export', [AdminController::class, 'exportReport'])->name('export');
    });
    
    // System Health
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/health', [AdminController::class, 'systemHealth'])->name('health');
    });
});
