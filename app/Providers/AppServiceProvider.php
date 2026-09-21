<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.admin', 'admin-layout');
        Blade::component('layouts.guest', 'guest-layout');

        Gate::before(function ($user, string $ability) {
            return $user->hasPermission($ability) ?: null;
        });
    }
}
