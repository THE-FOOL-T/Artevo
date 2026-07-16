<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
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
     * own role) is enforced by UpdateUserRoleRequest via UserPolicy.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $user->update(['role' => $request->validated('role')]);

        return back()->with('success', "{$user->name}'s role was updated to {$user->roleLabel()}.");
    }
}
