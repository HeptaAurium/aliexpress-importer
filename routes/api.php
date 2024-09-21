<?php

use Heptaaurium\AliexpressImporter\Http\Controllers\AuthController;
use Heptaaurium\AliexpressImporter\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['token.from.url'])->group(function () {
    Route::get('/', [AuthController::class, 'verifyToken']);
    Route::post('/aliexpress-importer/store-products', [ProductsController::class, 'store']);
});
