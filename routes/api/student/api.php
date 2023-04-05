<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:Student']], function(){
    Route::get('/profile', [AuthController::class,'profile'])->name('auth.profile');
});
