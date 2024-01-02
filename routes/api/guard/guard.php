<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\SecurityGuardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:security-guard']], function (){
    Route::get('/users', [UserController::class, 'guard_index'])->name('index.users');
    Route::post('/student-entry', [SecurityGuardController::class, 'studentEntry'])->name('student.entry');
    Route::get('/locations', [SecurityGuardController::class, 'location_index']);
    Route::post('/update-location', [SecurityGuardController::class, 'update'])->name('guard.update');
    Route::get('/violation-list', [SecurityGuardController::class, 'violationList'])->name('violation.list');
    Route::get('/user-violations', [SecurityGuardController::class, 'userViolationList']);
    Route::post('/violation', [SecurityGuardController::class, 'addViolation'])->name('add.violation');
});
