<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

/*
Route::group(['middleware' => ['auth:sanctum']], function() {
	Route::get('/', [AnalyticsController::class, 'index']);
});
*/
Route::group(['middleware' => ['auth:sanctum','role:admin']], function() {
	Route::get('/violation/type/{time}/{id}', [AnalyticsController::class, 'violationsByType']);
	Route::get('/violation/{time}/{id}', [AnalyticsController::class, 'violations']);
	//Route::get('/violation/gloss/{id}', [AnalyticsController::class, 'gloss']);
});