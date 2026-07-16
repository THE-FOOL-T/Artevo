<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(Login $event): void
    {
        $this->activityLogger->log(
            action: 'user.login',
            description: "{$event->user->name} logged in.",
            user: $event->user,
        );
    }
}
