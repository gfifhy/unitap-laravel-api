<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function() {
	Route::get('/my', [NotificationController::class, 'index']);
	Route::get('/mark-read/{id}', [NotificationController::class, 'mark']);
	Route::get('/mark-read-all', [NotificationController::class, 'markAll']);
});

Route::group(['middleware' => ['auth:sanctum','role:admin']], function() {
	Route::get('/', [NotificationController::class, '_all']);
	Route::post('/', [NotificationController::class, 'store']);
	Route::delete('/{id}', [NotificationController::class, 'destroy']);
});