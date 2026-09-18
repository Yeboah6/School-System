<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('view-system-dashboard', function (User $user): bool {
            return $user->hasAnyRole([
                'Super Administrator',
                'School Administrator',
                'Principal',
                'Vice Principal',
            ]);
        });

        Gate::define('manage-school-setup', function (User $user): bool {
            return $user->hasAnyRole([
                'Super Administrator',
                'School Administrator',
                'Principal',
                'Vice Principal',
            ]);
        });
    }
}
