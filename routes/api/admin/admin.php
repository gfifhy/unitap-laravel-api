<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:Admin']], function(){
    Route::post('/add/student', [AuthController::class,'addStudent'])->name('add.student');
    Route::get('/roles', [RoleController::class, 'index'])->name('index.role');
});


