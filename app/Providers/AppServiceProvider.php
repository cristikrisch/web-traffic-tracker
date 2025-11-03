<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('tracking', function (Request $request) {
            return [
                // Per IP cap
                Limit::perMinute(240)->by($request->ip()),
                // Per visitor key header cap
                Limit::perMinute(60)->by($request->header('x-vkey', $request->ip())),
            ];
        });
    }
}
