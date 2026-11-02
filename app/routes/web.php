<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Customer\MobileCartController;
use App\Http\Controllers\Customer\MobileCatalogController;
use App\Http\Controllers\Customer\MobileCheckoutController;
use App\Http\Controllers\Customer\MobileHistoryController;
use App\Http\Controllers\Customer\MobileOrderController;
use App\Http\Controllers\Customer\MobilePaymentController;
use App\Http\Controllers\Customer\WebCartController;
use App\Http\Controllers\Customer\WebCatalogController;
use App\Http\Controllers\Customer\WebCheckoutController;
use App\Http\Controllers\Customer\WebHistoryController;
use App\Http\Controllers\Customer\WebOrderController;
use App\Http\Controllers\Customer\WebPaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/', [WebCatalogController::class, 'home'])->name('web.home');
    Route::get('/menu', [WebCatalogController::class, 'menu'])->name('web.menu');
    Route::get('/cart', [WebCartController::class, 'show'])->name('web.cart');
    Route::post('/cart/add', [WebCartController::class, 'store'])->name('web.cart.add');
    Route::post('/cart/step', [WebCartController::class, 'step'])->name('web.cart.step');
    Route::post('/cart/update', [WebCartController::class, 'update'])->name('web.cart.update');
    Route::get('/checkout', [WebCheckoutController::class, 'show'])->name('web.checkout');
    Route::post('/checkout', [WebCheckoutController::class, 'store'])->name('web.checkout.store');
    Route::get('/payment/{code}', [WebPaymentController::class, 'show'])->name('web.payment');
    Route::get('/orders/{code}', [WebOrderController::class, 'show'])->name('web.orders.show');
    Route::post('/orders/{code}/reorder', [WebOrderController::class, 'reorder'])->name('web.orders.reorder');
    Route::get('/history', WebHistoryController::class)->name('web.history');
});

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

// Mobile Web Routes
Route::prefix('mobile')->middleware('throttle:60,1')->group(function () {
    Route::get('/', [MobileCatalogController::class, 'home'])->name('mobile.home');
    Route::get('/splash', [MobileCatalogController::class, 'splash'])->name('mobile.splash');
    Route::get('/menu', [MobileCatalogController::class, 'menu'])->name('mobile.menu');
    Route::get('/cart', [MobileCartController::class, 'show'])->name('mobile.cart');
    Route::post('/cart/add', [MobileCartController::class, 'store'])->name('mobile.cart.add');
    Route::post('/cart/step', [MobileCartController::class, 'step'])->name('mobile.cart.step');
    Route::post('/cart/update', [MobileCartController::class, 'update'])->name('mobile.cart.update');
    Route::post('/checkout', MobileCheckoutController::class)->name('mobile.checkout');
    Route::get('/payment/{code}', [MobilePaymentController::class, 'show'])->name('mobile.payment');
    Route::get('/orders/{code}', [MobileOrderController::class, 'show'])->name('mobile.orders.status');
    Route::post('/orders/{code}/reorder', [MobileOrderController::class, 'reorder'])->name('mobile.orders.reorder');
    Route::get('/history', MobileHistoryController::class)->name('mobile.history');
});
