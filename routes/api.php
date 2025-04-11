<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiSecretKey;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::group(['middleware' => CheckApiSecretKey::class], function () {
    // Routes cho Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
        Route::get('/{slug}/products', [CategoryController::class, 'products']);
    });

    // Routes cho Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/featured', [ProductController::class, 'featured']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/{slug}', [ProductController::class, 'show']);
        Route::get('/{slug}/reviews', [ProductController::class, 'reviews']);
    });

    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });
});
