<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Health check endpoint (no auth required for monitoring)
Route::get('/health', [HealthController::class, 'check']);
