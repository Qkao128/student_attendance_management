<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

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
        // RateLimiter::for('resetPasswordRateLimiter', function (Request $request) {
        //     return Limit::perMinutes(10, 3)->by($request->ip());
        // });
        Route::aliasMiddleware('check_role', \Spatie\Permission\Middleware\RoleMiddleware::class);
    }
}
