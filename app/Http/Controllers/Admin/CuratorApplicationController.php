<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuratorApplication;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuratorApplicationController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function index(): View
    {
        // Show pending first, then newest approved/rejected
        $applications = CuratorApplication::with(['applicant', 'admin'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        return view('admin.curator-applications.index', ['applications' => $applications]);
    }

    public function update(Request $request, CuratorApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($application->status !== CuratorApplication::STATUS_PENDING) {
            return back()->with('error', 'This application has already been processed.');
        }

        $application->update([
            'status'   => $validated['status'],
            'admin_id' => $request->user()->id,
        ]);

        $statusLabel = $validated['status'] === 'approved' ? 'approved' : 'rejected';

        // Log the decision
        $this->activityLogger->log(
            action: 'curator-application.' . $statusLabel,
            description: "{$request->user()->name} {$statusLabel} a curator application from {$application->applicant->name}.",
            user: $request->user(),
        );

        if ($validated['status'] === 'approved') {
            $user = $application->applicant;
            $previousRole = $user->role;
            
            // Actually change the role
            $user->update(['role' => User::ROLE_CURATOR]);

            // Log the role change just like the manual role change does
            $this->activityLogger->log(
                action: 'role.changed',
                description: "{$request->user()->name} changed {$user->name}'s role from {$previousRole} to " . User::ROLE_CURATOR . " (via application).",
                user: $request->user(),
                properties: [
                    'target_user_id' => $user->id,
                    'previous_role'  => $previousRole,
                    'new_role'       => User::ROLE_CURATOR,
                ]
            );
        }

        return back()->with('success', "Application from {$application->applicant->name} has been {$statusLabel}.");
    }
}
