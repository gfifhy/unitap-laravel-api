<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//rate limit
Route::group(['middleware' => ['throttle:loginThrottle']], function(){
    Route::post('/student/login', [AuthController::class,'studentLogin'])->name('auth.studentLogin');
    Route::post('/admin/login', [AuthController::class,'adminLogin'])->name('auth.adminLogin');
    Route::post('/staff/login', [AuthController::class,'staffLogin'])->name('auth.staffLogin');
    Route::get('/roles', [RoleController::class,'index'])->name('getRoles');
});

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('auth.profile');

});
