<?php

use App\Http\Controllers\API\LookupController;
use Illuminate\Support\Facades\Route;

Route::prefix('lookup')->group(function () {
   
        Route::get('/countries', [LookupController::class, 'countries']);
        
        Route::group(['middleware' => 'multi.auth'], function () {
            Route::get('/currencies', [LookupController::class, 'currencies']);
        });

}); 