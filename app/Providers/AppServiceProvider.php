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
        Blade::component('layouts.portal', 'portal-layout');

        // Every permission slug (roles.manage, projects.view, ...) becomes a
        // checkable ability. Returning null rather than false lets policies
        // still have their say for abilities that aren't permission slugs.
        //
        // A user holding a role flagged is_superadmin short-circuits both:
        // they get every ability in the admin panel without a single
        // permission being attached to their role.
        Gate::before(function ($user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->hasPermission($ability) ?: null;
        });
    }
}
