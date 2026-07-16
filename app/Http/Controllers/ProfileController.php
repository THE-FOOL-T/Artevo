<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\ActivityLogger;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private AvatarService $avatarService,
        private ActivityLogger $activityLogger,
    ) {
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's profile information (name, email,
     * avatar). Changing the email address resets email_verified_at so
     * the new address goes through verification again.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'email']);

        $avatarChanged = $request->hasFile('avatar');
        if ($avatarChanged) {
            $data['avatar_path'] = $this->avatarService->replace($user, $request->file('avatar'));
        }

        $emailChanged = $data['email'] !== $user->email;

        $user->fill($data);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->activityLogger->log(
            action: 'user.profile-updated',
            description: "{$user->name} updated their profile" . ($avatarChanged ? ' and avatar' : '') . '.',
            user: $user,
            properties: ['email_changed' => $emailChanged, 'avatar_changed' => $avatarChanged],
        );

        return back()->with('success', 'Your profile has been updated.');
    }

    /**
     * Permanently delete the authenticated user's account, after
     * confirming their current password.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Logged before deletion (with an explicit user_id) since the
        // activity_logs.user_id foreign key is set to null-on-delete —
        // the entry survives the account being removed, which is the
        // point of an audit trail.
        $this->activityLogger->log(
            action: 'user.account-deleted',
            description: "{$user->name} deleted their own account.",
            user: $user,
        );

        Auth::guard('web')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
