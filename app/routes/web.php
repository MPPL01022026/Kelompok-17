<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AyamoController;

Route::get('/', [AyamoController::class, 'shop']);

Route::get('/admin/login', [AyamoController::class, 'adminLogin']);
Route::post('/admin/login', [AyamoController::class, 'authenticate'])->middleware('throttle:10,1');
Route::middleware('admin.session')->group(function () {
    Route::get('/admin', [AyamoController::class, 'dashboard']);
    Route::post('/admin/logout', [AyamoController::class, 'logout']);
    Route::post('/admin/orders/{id}/confirm', [AyamoController::class, 'confirmOrder']);
    Route::post('/admin/products', [AyamoController::class, 'saveProduct']);
    Route::post('/admin/products/{id}', [AyamoController::class, 'saveProduct']);
    Route::post('/admin/products/{id}/delete', [AyamoController::class, 'deleteProduct']);
    Route::post('/admin/transactions', [AyamoController::class, 'manualSale']);
});
