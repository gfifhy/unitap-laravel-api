<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:Admin']], function(){
    Route::get('/add/student', [AuthController::class,'addStudent'])->name('add.student');
});


