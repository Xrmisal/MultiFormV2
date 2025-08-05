<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::resource('/leads', LeadController::class);

