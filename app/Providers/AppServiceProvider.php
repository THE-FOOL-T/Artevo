<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Fires the built-in verification email whenever a new user
        // registers (App\Models\User implements MustVerifyEmail).
        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        // Applied everywhere a `Password::defaults()` rule is used
        // (RegisterRequest, ChangePasswordRequest, NewPasswordController).
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        Gate::policy(User::class, UserPolicy::class);

        // Simple, role-based decisions that don't need a full Policy.
        // Consumed by later phases: access-analytics/export-reports gate
        // the Analytics/Reports pages (Phase 17), view-activity-logs
        // gates the activity log viewer (Phase 4).
        Gate::define('view-dashboard', fn (User $user) => true);
        Gate::define('access-analytics', fn (User $user) => $user->isAdmin());
        Gate::define('export-reports', fn (User $user) => $user->isAdmin());
        Gate::define('view-activity-logs', fn (User $user) => $user->isAdmin());
    }
}
