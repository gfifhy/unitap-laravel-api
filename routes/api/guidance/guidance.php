<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:guidance-staff']], function () {
    Route::get('/data/location', [ResourceController::class, 'getCountOfStudentPerLocation'])->name('data.location');
    Route::get('/data/violation', [ResourceController::class, 'totalViolation'])->name('data.violation');
});


