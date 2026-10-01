<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Every API response is already wrapped in the {success, message, data}
        // envelope by App\Http\Concerns\ApiResponses, so resource collections
        // must not add their own "data" wrapper on top of it.
        JsonResource::withoutWrapping();

        // This is an API-only backend: an unauthenticated request must
        // always get a JSON 401, never a redirect to a "login" web route
        // that doesn't exist here (which would otherwise surface as an
        // unrelated 500 when the client omits an Accept: application/json
        // header).
        Authenticate::redirectUsing(fn () => null);

        // Public lead-capture endpoints: IP-based throttling as a first line
        // of anti-spam defence. A CAPTCHA/Turnstile check can later slot into
        // the same route middleware stack without restructuring anything.
        RateLimiter::for('lead-capture', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Public read endpoints (services, work, pricing): generous but
        // bounded, so scraping/flooding can't exhaust the database.
        RateLimiter::for('public-read', function ($request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Admin login: throttled per IP + email pair so a single attacker
        // can't lock out a legitimate admin by hammering their address.
        RateLimiter::for('admin-login', function ($request) {
            return Limit::perMinute(5)->by($request->ip().'|'.Str::lower((string) $request->input('email')));
        });
    }
}
