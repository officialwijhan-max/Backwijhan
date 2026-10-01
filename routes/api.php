<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\QuoteRequestController as AdminQuoteRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public — content read by the marketing site.
    Route::middleware('throttle:public-read')->group(function () {
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/work', [WorkController::class, 'index']);
        Route::get('/work/{slug}', [WorkController::class, 'show']);
        Route::get('/pricing', [PricingController::class, 'index']);
    });

    // Public — lead capture. Rate limited per IP; the room for a future
    // CAPTCHA/Turnstile check is the same route middleware stack.
    Route::middleware('throttle:lead-capture')->group(function () {
        Route::post('/contact', [ContactController::class, 'store']);
        Route::post('/quote-requests', [QuoteRequestController::class, 'store']);
    });

    // Admin
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:admin-login');

        Route::middleware(['auth:sanctum', 'active.admin'])->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/me', [AdminAuthController::class, 'me']);

            Route::get('/dashboard', [AdminDashboardController::class, 'index']);

            Route::get('/contacts', [AdminContactController::class, 'index']);
            Route::get('/contacts/{contact}', [AdminContactController::class, 'show']);
            Route::patch('/contacts/{contact}', [AdminContactController::class, 'update']);
            Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy']);

            Route::get('/quote-requests', [AdminQuoteRequestController::class, 'index']);
            Route::get('/quote-requests/{quoteRequest}', [AdminQuoteRequestController::class, 'show']);
            Route::patch('/quote-requests/{quoteRequest}', [AdminQuoteRequestController::class, 'update']);
            Route::delete('/quote-requests/{quoteRequest}', [AdminQuoteRequestController::class, 'destroy']);
        });
    });
});
