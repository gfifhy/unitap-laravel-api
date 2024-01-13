<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:store']], function () {
    Route::post('/product', [ProductController::class, 'store'])->name('add.product');
    Route::get('/product', [ProductController::class, 'index'])->name('index.product');
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('show.product');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('delete.product');
    Route::post('/wallet-status', [ResourceController::class, 'walletStatus'])->name('toggle.wallet');
    //Route::get('/order', [ResourceController::class, 'orderIndex'])->name('all.orders');
    Route::get('/orders', [OrderController::class, 'getStoreOrders']);
    Route::post('/order/fulfill', [OrderController::class, 'fulfillOrder']);
    Route::post('/complete-order/{id}', [ResourceController::class, 'completeOrder'])->name('completeOrder');
    Route::get('/transactions', [TransactionController::class, 'getRecentTransactions']);
});
