<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum','role:admin']], function(){
	Route::post('/', [LandingPageController::class, 'store']);
});