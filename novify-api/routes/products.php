<?php

use App\Http\Controllers\API\Merchant\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {

    Route::prefix('temp')->group(function () {
        Route::get('/categories', [ProductController::class, 'getTempCategories']);
        Route::get('/measure-units', [ProductController::class, 'getTempMeasureUnits']);
        Route::post('/categories', [ProductController::class, 'chooseTempCategories']);
        Route::post('/measure-units', [ProductController::class, 'chooseTempMeasureUnits']);
    });

    Route::middleware('auth:api')->group(function () {

    // Products
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);

    // Product Variants
    Route::get('/{product}/variants', [ProductController::class, 'variants']);
    Route::post('/{product}/variants', [ProductController::class, 'storeVariant']);
    Route::put('/{product}/variants/{variant}', [ProductController::class, 'updateVariant']);
    Route::delete('/{product}/variants/{variant}', [ProductController::class, 'deleteVariant']);

    // Product Images
    Route::post('/{product}/images', [ProductController::class, 'storeImage']);
    Route::put('/{product}/images/{image}', [ProductController::class, 'updateImage']);
    Route::delete('/{product}/images/{image}', [ProductController::class, 'deleteImage']);
    Route::post('/{product}/images/reorder', [ProductController::class, 'reorderImages']);

    // Stock Management
    Route::put('/{product}/stock', [ProductController::class, 'updateStock']);
    Route::put('/{product}/variants/{variant}/stock', [ProductController::class, 'updateVariantStock']);

    // Product Categories
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::post('/categories', [ProductController::class, 'storeCategory']);
    Route::get('/categories/{category}', [ProductController::class, 'showCategory']);
    Route::put('/categories/{category}', [ProductController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [ProductController::class, 'deleteCategory']);

    // Product Measure Units
    Route::get('/measure-units', [ProductController::class, 'measureUnits']);
    Route::post('/measure-units', [ProductController::class, 'storeMeasureUnit']);
    Route::get('/measure-units/{unit}', [ProductController::class, 'showMeasureUnit']);
    Route::put('/measure-units/{unit}', [ProductController::class, 'updateMeasureUnit']);
    Route::delete('/measure-units/{unit}', [ProductController::class, 'deleteMeasureUnit']);

    });
        

}); 


