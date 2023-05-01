<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:student']], function () {
    Route::post('/violation', [ResourceController::class, 'getViolationForStudent'])->name('getViolation');
    Route::post('/wallet-status', [ResourceController::class, 'walletStatus'])->name('toggle.wallet');
    Route::get('/store', [StudentController::class, 'indexStore'])->name('index.store');
    Route::get('/store/{id}', [StudentController::class, 'storeProduct'])->name('store.products');
    Route::post('/order', [StudentController::class, 'order'])->name('order.products');
});


