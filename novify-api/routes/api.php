<?php

use App\Http\Controllers\API\TestController;
use Illuminate\Support\Facades\Route;

// Include all route files
Route::get('/test', [TestController::class, 'register']);

Route::middleware('cors')->group(function () {
    require __DIR__.'/auth.php';
    require __DIR__.'/merchant.php';
    require __DIR__.'/lookup.php';
    require __DIR__.'/wallet.php';
    require __DIR__.'/products.php';
    require __DIR__.'/orders.php';
});
