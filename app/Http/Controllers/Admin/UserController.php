<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserRoleChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List every user with their current role, newest first. Paginated
     * since this will grow well beyond one page in production.
     */
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(20),
        ]);
    }

    /**
     * Change a user's role. Authorization (admin-only, can't change your
     * own role) is enforced by SaveUserRoleRequest via UserPolicy.
     */
    public function updateRole(SaveUserRoleRequest $request, User $user): RedirectResponse
    {
        $previousRole = $user->role;
        $newRole = $request->validated('role');

        $user->update(['role' => $newRole]);

        // Two listeners consume this: LogRoleChange (activity log) and
        // SendRoleChangedNotification (emails + notifies the user).
        if ($previousRole !== $newRole) {
            UserRoleChanged::dispatch($user, $request->user(), $previousRole, $newRole);
        }

        return back()->with('success', "{$user->name}'s role was updated to {$user->roleLabel()}.");
    }
}
