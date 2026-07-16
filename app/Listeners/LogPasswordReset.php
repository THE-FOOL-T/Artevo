<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(PasswordReset $event): void
    {
        $this->activityLogger->log(
            action: 'user.password-reset',
            description: "{$event->user->name} reset their password via the forgot-password flow.",
            user: $event->user,
        );
    }
}
