<?php

use App\Http\Controllers\API\Bills\BillCategoryController;
use App\Http\Controllers\API\Bills\BillerController;
use App\Http\Controllers\API\Bills\BillPaymentController;
use Illuminate\Support\Facades\Route;

// Bill Payment Routes
Route::prefix('bills')->group(function () {
    // Bill Categories
    Route::get('categories', [BillCategoryController::class, 'index']);
    Route::get('categories/{category}', [BillCategoryController::class, 'show']);

    // Billers
    Route::get('billers', [BillerController::class, 'index']);
    Route::get('billers/{biller}', [BillerController::class, 'show']);

    // Bill Payments
    Route::middleware('multi.auth')->group(function () {
        Route::post('validate', [BillPaymentController::class, 'validate']);
        Route::post('pay', [BillPaymentController::class, 'process']);
        Route::get('payments/{reference}', [BillPaymentController::class, 'show']);
    });
});
