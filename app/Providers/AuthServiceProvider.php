<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('teacher', function (User $user) {
            return is_null($user->teacher_user_id);
        });

        Gate::define('monitor', function (User $user) {
            return !is_null($user->teacher_user_id);
        });
    }
}
