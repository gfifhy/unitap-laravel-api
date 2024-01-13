<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum','role:student']], function () {
    Route::post('/violation', [ResourceController::class, 'getViolationForStudent'])->name('getViolation');
    Route::post('/wallet-status', [ResourceController::class, 'walletStatus'])->name('toggle.wallet');
    Route::get('/store', [StudentController::class, 'indexStore'])->name('index.store');
    Route::get('/store/{id}', [StudentController::class, 'storeProduct'])->name('store.products');
    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'getProduct']);
    Route::post('/order', [StudentController::class, 'order'])->name('order.products');
    //Route::post('/test', [StudentController::class,'testFunction'])->name('student.test.route');
    Route::post('/order-product', [OrderController::class, 'orderProduct']);
    Route::get('/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/transfers', [TransactionController::class, 'getUserTransfers']);
    Route::get('/transactions', [TransactionController::class, 'getRecentTransactions']);
});


