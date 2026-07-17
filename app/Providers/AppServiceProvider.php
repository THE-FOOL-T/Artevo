<?php

namespace App\Providers;

use App\Events\MuseumVerificationStatusChanged;
use App\Events\UserRoleChanged;
use App\Listeners\LogEmailVerified;
use App\Listeners\LogMuseumVerificationChange;
use App\Listeners\LogPasswordReset;
use App\Listeners\LogRoleChange;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogUserLogout;
use App\Listeners\LogUserRegistration;
use App\Listeners\SendMuseumVerificationNotification;
use App\Listeners\SendRoleChangedNotification;
use App\Models\Artifact;
use App\Models\Auction;
use App\Models\Collection;
use App\Models\Exhibition;
use App\Models\Museum;
use App\Models\User;
use App\Policies\ArtifactPolicy;
use App\Policies\AuctionPolicy;
use App\Policies\CollectionPolicy;
use App\Policies\ExhibitionPolicy;
use App\Policies\MuseumPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Pagination\Paginator;
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

        // Phase 4 — activity log listeners. Login/Logout/Registered/
        // Verified/PasswordReset are all fired automatically by
        // Laravel's built-in auth system; only UserRoleChanged is
        // dispatched manually (Admin\UserController).
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogUserLogout::class);
        Event::listen(Registered::class, LogUserRegistration::class);
        Event::listen(Verified::class, LogEmailVerified::class);
        Event::listen(PasswordReset::class, LogPasswordReset::class);
        Event::listen(UserRoleChanged::class, LogRoleChange::class);
        Event::listen(UserRoleChanged::class, SendRoleChangedNotification::class);
        Event::listen(MuseumVerificationStatusChanged::class, LogMuseumVerificationChange::class);
        Event::listen(MuseumVerificationStatusChanged::class, SendMuseumVerificationNotification::class);

        // Applied everywhere a `Password::defaults()` rule is used
        // (RegisterRequest, ChangePasswordRequest, NewPasswordController).
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Museum::class, MuseumPolicy::class);
        Gate::policy(Artifact::class, ArtifactPolicy::class);
        Gate::policy(Auction::class, AuctionPolicy::class);
        Gate::policy(Collection::class, CollectionPolicy::class);
        Gate::policy(Exhibition::class, ExhibitionPolicy::class);

        // Simple, role-based decisions that don't need a full Policy.
        // access-analytics/export-reports are consumed by the Analytics/
        // Reports pages (Phase 17); view-activity-logs gates the Phase 4
        // admin activity log viewer below.
        Gate::define('view-dashboard', fn (User $user) => true);
        Gate::define('access-analytics', fn (User $user) => $user->isAdmin());
        Gate::define('export-reports', fn (User $user) => $user->isAdmin());
        Gate::define('view-activity-logs', fn (User $user) => $user->isAdmin());

        // Every existing ->links() call across the app (museums,
        // artifacts, admin users, activity logs...) picks this up
        // automatically — no per-view changes needed.
        Paginator::defaultView('pagination.av');
        Paginator::defaultSimpleView('pagination.av');
    }
}
