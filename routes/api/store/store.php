<?php


use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:store']], function () {
    Route::post('/product', [ProductController::class, 'store'])->name('add.product');
    Route::get('/product', [ProductController::class, 'index'])->name('index.product');
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('show.product');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('delete.product');
});


