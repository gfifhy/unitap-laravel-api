<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use Illuminate\Support\Facades\Route;

//rate limit
Route::group(['middleware' => ['throttle:loginThrottle']], function(){
    Route::post('/student/login', [AuthController::class,'studentLogin'])->name('auth.studentLogin');
    Route::post('/admin/login', [AuthController::class,'adminLogin'])->name('auth.adminLogin');
    Route::post('/staff/login', [AuthController::class,'staffLogin'])->name('auth.staffLogin');
    Route::get('/roles', [RoleController::class,'index'])->name('getRoles');
});

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
    Route::get('/asset/{image}', [ResourceController::class, 'download'])->name('image.download')->where('image', '.*');

});

Route::prefix('webauth')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/register/options', [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
        Route::post('/register', [WebAuthnRegisterController::class, 'register'])->name('webauthn.register');
    });

Route::prefix('webauth')
    ->group(function () {
        Route::get('/login/options', [WebAuthnLoginController::class, 'options'])->name('webauthn.login.options');
        Route::post('/login', [WebAuthnLoginController::class, 'login'])->name('webauthn.login');
    });