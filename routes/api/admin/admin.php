<?php


use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin']], function(){
    Route::post('/student', [ResourceController::class,'addStudent'])->name('add.student');
    Route::post('/staff', [ResourceController::class, 'addStaff'])->name('add.staff');
    Route::get('/users', [UserController::class, 'index'])->name('index.users');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('show.users');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('archive.users');
    Route::get('/roles', [RoleController::class, 'index'])->name('index.role');
});


