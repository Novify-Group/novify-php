<?php

use App\Http\Controllers\API\Merchant\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {


    Route::prefix('lists')->group(function () {
        Route::get('/categories', [ProductController::class, 'categories']);
        Route::get('/measure-units', [ProductController::class, 'measureUnits']);
        Route::get('/categories/{category}', [ProductController::class, 'showCategory']);
        Route::get('/measure-units/{unit}', [ProductController::class, 'showMeasureUnit']);
    });

    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::get('/{product}/variants', [ProductController::class, 'variants']);
    Route::post('/{product}/images', [ProductController::class, 'storeImage']);

    Route::middleware('auth:api')->group(function () {
        
        // Temp Data
        Route::prefix('temp')->group(function () {
            Route::get('/categories', [ProductController::class, 'getTempCategories']);
            Route::get('/measure-units', [ProductController::class, 'getTempMeasureUnits']);
            Route::post('/categories', [ProductController::class, 'chooseTempCategories']);
            Route::post('/measure-units', [ProductController::class, 'chooseTempMeasureUnits']);
            Route::post('/setup', [ProductController::class, 'chooseTempData']);
        });

        // Products
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);

        // Product Variants
        Route::post('/{product}/variants', [ProductController::class, 'storeVariant']);
        Route::put('/{product}/variants/{variant}', [ProductController::class, 'updateVariant']);
        Route::delete('/{product}/variants/{variant}', [ProductController::class, 'deleteVariant']);

        // Product Images
        Route::put('/{product}/images/{image}', [ProductController::class, 'updateImage']);
        Route::delete('/{product}/images/{image}', [ProductController::class, 'deleteImage']);
        Route::post('/{product}/images/reorder', [ProductController::class, 'reorderImages']);

        // Stock Management
        Route::put('/{product}/stock', [ProductController::class, 'updateStock']);
        Route::put('/{product}/variants/{variant}/stock', [ProductController::class, 'updateVariantStock']);

        // Product Categories
        Route::prefix('lists/categories')->group(function () {
            Route::post('/', [ProductController::class, 'storeCategory']);
            Route::put('/{category}', [ProductController::class, 'updateCategory']);
            Route::delete('/{category}', [ProductController::class, 'deleteCategory']);
        });

        // Product Measure Units
        Route::prefix('lists/measure-units')->group(function () {
            Route::post('/', [ProductController::class, 'storeMeasureUnit']);
            Route::put('/{unit}', [ProductController::class, 'updateMeasureUnit']);
            Route::delete('/{unit}', [ProductController::class, 'deleteMeasureUnit']);
        });
            
    });

}); 
