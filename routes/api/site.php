<?php

use App\Http\Controllers\SiteMiscController;
use Illuminate\Support\Facades\Route;

Route::get('/logotext', [SiteMiscController::class, 'getLogoText']);

Route::group(['middleware' => ['auth:sanctum','role:admin']], function(){
	Route::post('/pictures', [SiteMiscController::class, 'setPictures']);
});