<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        Gate::define('manage-catalog', fn($user) => in_array($user->role, ['admin','catalog']));
        Gate::define('manage-orders', fn($user) => in_array($user->role, ['admin','cs']));
        Gate::define('admin-only', fn($user) => $user->role === 'admin');
    }
}
