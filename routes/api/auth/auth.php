<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//rate limit
Route::group(['middleware' => ['throttle:verifyUser']], function(){
    Route::post('/register', [AuthController::class,'register'])->name('auth.register');
    Route::post('/student-login', [AuthController::class,'studentLogin'])->name('auth.studentLogin');
});
