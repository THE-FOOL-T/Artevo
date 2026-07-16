<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Notifications\PasswordChangedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    /**
     * Update the authenticated user's password from their profile page.
     * (For the "I forgot my password" flow, see NewPasswordController.)
     */
    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $this->activityLogger->log(
            action: 'user.password-changed',
            description: "{$user->name} changed their password from the profile page.",
            user: $user,
        );

        $user->notify(new PasswordChangedNotification());

        return back()->with('success', 'Your password has been updated.');
    }
}
