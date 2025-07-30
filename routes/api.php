<?php

use App\Http\Controllers\LeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/lead', LeadController::class);

