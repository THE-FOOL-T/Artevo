<?php

namespace App\Http\Controllers;

use App\Models\CuratorApplication;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuratorApplicationController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // If they are already a curator or admin, they don't need to apply.
        if ($user->isCurator() || $user->isAdmin()) {
            return redirect()->route('dashboard')->with('success', 'You are already a curator.');
        }

        // If they have a pending application, show them that instead.
        if ($user->curatorApplication?->isPending()) {
            return redirect()->route('dashboard')->with('success', 'Your curator application is currently under review.');
        }

        return view('curator-applications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isCurator() || $user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        if ($user->curatorApplication?->isPending()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'job_title'        => 'required|string|max:255',
            'justification'    => 'required|string|max:2000',
        ]);

        // Create or update application (if they were rejected before, they can re-apply)
        $application = CuratorApplication::updateOrCreate(
            ['user_id' => $user->id],
            [
                'institution_name' => $validated['institution_name'],
                'job_title'        => $validated['job_title'],
                'justification'    => $validated['justification'],
                'status'           => CuratorApplication::STATUS_PENDING,
            ]
        );

        $this->activityLogger->log(
            action: 'curator-application.submitted',
            description: "{$user->name} applied for the Curator role.",
            user: $user,
        );

        return redirect()->route('dashboard')
            ->with('success', 'Your curator application has been submitted and is pending review by our team.');
    }
}
