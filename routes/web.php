<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Health check endpoint
Route::get('/health', [HealthController::class, 'check']);

// OAuth2 routes for email account connections
Route::get('/auth/{provider}/connect', [OAuthController::class, 'connect'])
    ->name('oauth.connect')
    ->where('provider', 'google|microsoft');

Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])
    ->name('oauth.callback')
    ->where('provider', 'google|microsoft');

Route::post('/auth/{provider}/disconnect/{accountId}', [OAuthController::class, 'disconnect'])
    ->name('oauth.disconnect')
    ->where('provider', 'google|microsoft');
