<?php

use App\Http\Controllers\API\Merchant\BranchController;
use App\Http\Controllers\API\Merchant\UserController;
use App\Http\Controllers\API\MerchantController;
use Illuminate\Support\Facades\Route;

Route::prefix('merchant')->middleware('auth:api')->group(function () {

    Route::get('/', [MerchantController::class, 'getMerchants']);
    Route::get('/{wallet_number}', [MerchantController::class, 'getMerchantByWalletNumber']);

    // Branch management
    Route::prefix('branches')->group(function () {
        Route::post('/', [BranchController::class, 'store']);
        Route::get('/', [BranchController::class, 'index']);
        Route::get('/{branch}', [BranchController::class, 'show']);
        Route::put('/{branch}', [BranchController::class, 'update']);
        Route::delete('/{branch}', [BranchController::class, 'destroy']);
    });

    // User management (attendants, distributors)
    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'storeAttendant']);
        Route::get('/', [UserController::class, 'listAttendants']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword']);
    });
}); 