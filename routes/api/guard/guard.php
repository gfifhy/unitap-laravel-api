<?php


use App\Http\Controllers\SecurityGuardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:security-guard']], function (){
    Route::post('/student-entry', [SecurityGuardController::class, 'studentEntry'])->name('student.entry');
});
