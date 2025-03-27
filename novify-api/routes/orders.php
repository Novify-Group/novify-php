<?php

use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('merchant/orders')->middleware('auth:api')->group(function () {

        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        
}); 