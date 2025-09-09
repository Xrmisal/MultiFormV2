<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::middleware("auth:sanctum")->group(function() {
    Route::resource('/leads', LeadController::class);
    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('/user', [AuthController::class,'getUser']);
});

Route::post('/login', [AuthController::class,'login'])->middleware('throttle:10,1');

Route::post('/register', [AuthController::class,'register'])->middleware('throttle:10,1');

