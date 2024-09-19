<?php

use Heptaaurium\AliexpressImporter\Http\Controllers\AuthController;
use Heptaaurium\AliexpressImporter\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'HeptaAurium\AliExpressImporter\Controllers', 'middleware' => 'web'], function () {
    Route::group(['prefix' => 'aliexpress-importer'], function () {
        Route::get('/create-token', [AuthController::class, 'createToken'])->name('aliexpressimporter.token.create');
        Route::post('create-token', [AuthController::class, 'storeToken']);
        Route::delete('/delete-token/{id}', [AuthController::class, 'deleteToken'])->name('aliexpressimporter.token.delete');
    });
});
