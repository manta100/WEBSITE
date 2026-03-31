<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/search', [ProductController::class, 'search']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
    });
    
    Route::post('auth/token', [AuthController::class, 'token']);
    Route::delete('auth/token', [AuthController::class, 'revoke'])->middleware('auth:sanctum');
});
