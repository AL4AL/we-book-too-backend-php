<?php

use App\Http\Controllers\ImageProxyController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
Route::get('hi', function () {
    return 'bye';
});
Route::prefix('v1')->group(function () {
    Route::get('images/{size}/home-page-background.webp', [ImageProxyController::class, 'serve'])
       ->whereIn('size', ['base', 'sm', 'lg']);

    Route::get('health', [HealthController::class, 'index']);

    // Public catalog
    Route::get('catalog/services', [CatalogController::class, 'services']);
    Route::get('catalog/categories', [CatalogController::class, 'categories']);
    Route::get('catalog/specialists', [CatalogController::class, 'specialists']);
    Route::get('catalog/featured', [CatalogController::class, 'featured']);

    // Auth
    Route::post('auth/otp/request', [OtpController::class, 'requestOtp']);
    Route::post('auth/otp/verify', [OtpController::class, 'verifyOtp']);
    Route::post('auth/google/callback', [SocialiteController::class, 'googleCallback']);
    Route::post('auth/telegram/callback', [SocialiteController::class, 'telegramCallback']);
    Route::post('auth/logout', [SocialiteController::class, 'logout'])->middleware('auth:sanctum');

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // Bookings
        Route::apiResource('bookings', BookingController::class);
        
        // Payments
        Route::get('payments/gateways', [PaymentController::class, 'gateways']);
        Route::post('payments/intents', [PaymentController::class, 'createIntent']);
        Route::post('payments/calculate', [PaymentController::class, 'calculate']);
        
        // Profile
        Route::get('me', [ProfileController::class, 'me']);
        Route::get('me/profile', [ProfileController::class, 'show']);
        Route::put('me/profile', [ProfileController::class, 'update']);
        
        // Chat
        Route::post('chat/requests', [ChatController::class, 'requestChat']);
        Route::post('chat/requests/{conversation}/accept', [ChatController::class, 'acceptChat']);
        Route::get('chat/conversations', [ChatController::class, 'conversations']);
        Route::get('chat/conversations/{conversation}/messages', [ChatController::class, 'messages']);
        Route::post('chat/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
    });

    // Metrics (lock down in production)
    Route::get('metrics', MetricsController::class);
});