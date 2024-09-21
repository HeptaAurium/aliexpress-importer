<?php

use Heptaaurium\AliexpressImporter\Http\Controllers\AuthController;
use Heptaaurium\AliexpressImporter\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['token.from.url'])->group(function () {
    Route::get('/', [AuthController::class, 'verifyToken']);
    Route::get('/aliexpress-importer/store-products/{productId}', [ProductsController::class, 'store']);
});
