<?php


use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:Admin']], function(){
    Route::post('/add/student', [ResourceController::class,'addStudent'])->name('add.student');
    Route::post('add/security-guard', [ResourceController::class, 'addStaff'])->name('add.security-guard');
    Route::post('add/store', [ResourceController::class, 'addStaff'])->name('add.store');
    Route::get('/roles', [RoleController::class, 'index'])->name('index.role');
});


