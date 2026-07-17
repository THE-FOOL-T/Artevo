<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveDonationRequest;
use App\Models\Artifact;
use App\Models\Donation;
use App\Models\Museum;
use App\Services\ActivityLogger;
use App\Services\DonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(
        private DonationService $donationService,
        private ActivityLogger  $activityLogger,
    ) {}

    /**
     * List the authenticated user's donation requests.
     */
    public function index(Request $request): View
    {
        $donations = Donation::with(['artifact.images', 'museum'])
            ->where('donor_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('donations.index', compact('donations'));
    }

    /**
     * Show the donation request form.
     * Pre-selects the artifact from the query string: ?artifact=slug
     */
    public function create(Request $request): View
    {
        $artifact = null;

        if ($request->has('artifact')) {
            $artifact = Artifact::where('slug', $request->query('artifact'))->firstOrFail();
            Gate::authorize('donate', $artifact);
        }

        // Only verified museums are valid recipients
        $museums = Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'country']);

        return view('donations.create', compact('artifact', 'museums'));
    }

    /**
     * Store a new donation request.
     */
    public function store(SaveDonationRequest $request): RedirectResponse
    {
        // artifact_slug is a plain hidden field — not part of the validated donation data
        $artifact = Artifact::where('slug', $request->input('artifact_slug'))->firstOrFail();

        Gate::authorize('donate', $artifact);

        $validated = $request->validated();
        $museum    = Museum::findOrFail($validated['museum_id']);

        $donation = $this->donationService->requestDonation(
            $artifact,
            $request->user(),
            $museum,
            $validated,
        );

        $this->activityLogger->log(
            action: 'donation.requested',
            description: "{$request->user()->name} submitted a donation request for \"{$artifact->name}\" to {$museum->name}.",
            subject: $donation,
            user: $request->user(),
        );

        return redirect()
            ->route('donations.show', $donation)
            ->with('success', 'Your donation request has been submitted. The museum will be notified.');
    }


    /**
     * Show a single donation request.
     */
    public function show(Donation $donation): View
    {
        Gate::authorize('view', $donation);

        $donation->load(['artifact.images', 'museum', 'donor', 'reviewer']);

        return view('donations.show', compact('donation'));
    }

    /**
     * Cancel a pending donation request (donor only).
     */
    public function destroy(Request $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('cancel', $donation);

        $artifactName = $donation->artifact->name;

        $this->donationService->cancelDonation($donation);

        $this->activityLogger->log(
            action: 'donation.cancelled',
            description: "{$request->user()->name} cancelled their donation request for \"{$artifactName}\".",
            subject: null,
            user: $request->user(),
        );

        return redirect()
            ->route('donations.index')
            ->with('success', 'Donation request cancelled.');
    }
}
