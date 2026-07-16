<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(Logout $event): void
    {
        // $event->user can be null if the session was already cleared
        // before this fires — nothing to log in that case.
        if (! $event->user) {
            return;
        }

        $this->activityLogger->log(
            action: 'user.logout',
            description: "{$event->user->name} logged out.",
            user: $event->user,
        );
    }
}
