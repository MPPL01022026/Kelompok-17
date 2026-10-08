<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;

Route::get('/', [ShopController::class, 'shop']);
Route::post('/cart/add', [ShopController::class, 'addToCart']);
Route::post('/cart/update', [ShopController::class, 'updateCart']);
Route::post('/checkout', [ShopController::class, 'checkout']);

Route::get('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/login', [AdminAuthController::class, 'authenticate'])->middleware('throttle:10,1');

Route::middleware('admin.session')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::post('/admin/orders/{id}/confirm', [AdminOrderController::class, 'confirm']);
    Route::post('/admin/products', [AdminProductController::class, 'store']);
    Route::post('/admin/products/{id}', [AdminProductController::class, 'store']);
    Route::post('/admin/products/{id}/delete', [AdminProductController::class, 'destroy']);
    Route::post('/admin/transactions', [AdminTransactionController::class, 'manualSale']);
});

