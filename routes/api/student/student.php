<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:student']], function () {
    Route::post('/violation', [ResourceController::class, 'getViolationForStudent'])->name('getViolation');
});


