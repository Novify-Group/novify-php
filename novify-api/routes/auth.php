<?php

use App\Http\Controllers\API\MerchantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Merchant\BranchUserAuthController;

Route::prefix('auth')->group(function () {
    // Public auth routes
    Route::post('/register', [MerchantController::class, 'register']);
    Route::post('/login', [MerchantController::class, 'login']);
    Route::post('/forgot-password', [MerchantController::class, 'forgotPassword']);
    Route::post('/reset-password', [MerchantController::class, 'resetPassword']);
    Route::post('/attendant-login', [BranchUserAuthController::class, 'login']);

    // Protected auth routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/verify-otp', [MerchantController::class, 'verifyOtp']);
        Route::post('/resend-otp', [MerchantController::class, 'resendOtp']);
        Route::post('/change-password', [MerchantController::class, 'changePassword']);
        Route::post('/logout', [MerchantController::class, 'logout']);
        Route::post('/refresh', [MerchantController::class, 'refresh']);
    });
}); 