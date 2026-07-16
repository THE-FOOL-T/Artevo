<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Registered;

class LogUserRegistration
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(Registered $event): void
    {
        $this->activityLogger->log(
            action: 'user.registered',
            description: "{$event->user->name} created an account.",
            user: $event->user,
        );
    }
}
