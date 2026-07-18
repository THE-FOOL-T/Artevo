<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewDonationRequest;
use App\Models\Donation;
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
     * List all donation requests, filterable by status tab.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $donations = Donation::with(['artifact.images', 'museum', 'donor'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20);

        $counts = [
            'pending'     => Donation::where('status', Donation::STATUS_PENDING)->count(),
            'approved'    => Donation::where('status', Donation::STATUS_APPROVED)->count(),
            'transferred' => Donation::where('status', Donation::STATUS_TRANSFERRED)->count(),
            'rejected'    => Donation::where('status', Donation::STATUS_REJECTED)->count(),
        ];

        return view('admin.donations.index', compact('donations', 'status', 'counts'));
    }

    /**
     * Show a single donation for admin review.
     */
    public function show(Donation $donation): View
    {
        Gate::authorize('review', $donation);

        $donation->load(['artifact.images', 'artifact.museum', 'museum', 'donor', 'reviewer']);

        return view('admin.donations.show', compact('donation'));
    }

    /**
     * Admin approves or rejects a donation request.
     */
    public function review(ReviewDonationRequest $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('review', $donation);

        if (! $donation->isPending()) {
            return back()->with('error', 'This donation has already been reviewed.');
        }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            // Optionally store a provenance note before transfer
            if (! empty($validated['provenance_note'])) {
                $donation->update(['provenance_note' => $validated['provenance_note']]);
            }

            $this->donationService->approveDonation($donation, $request->user());

            $this->activityLogger->log(
                action: 'donation.approved',
                description: "{$request->user()->name} approved the donation of \"{$donation->artifact->name}\".",
                subject: $donation,
                user: $request->user(),
            );

            return redirect()
                ->route('admin.donations.show', $donation)
                ->with('success', 'Donation approved. You can now complete the ownership transfer.');
        }

        // Reject path
        $this->donationService->rejectDonation(
            $donation,
            $request->user(),
            $validated['rejection_reason'],
        );

        $this->activityLogger->log(
            action: 'donation.rejected',
            description: "{$request->user()->name} rejected the donation of \"{$donation->artifact->name}\".",
            subject: $donation,
            user: $request->user(),
        );

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation request rejected. The donor has been notified.');
    }

    /**
     * Admin triggers final ownership transfer for an approved donation.
     */
    public function transfer(Request $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('transfer', $donation);

        if (! $donation->isApproved()) {
            return back()->with('error', 'Only approved donations can be transferred.');
        }

        $this->donationService->transferOwnership($donation);

        $this->activityLogger->log(
            action: 'donation.transferred',
            description: "{$request->user()->name} completed ownership transfer for \"{$donation->artifact->name}\" to {$donation->museum->name}.",
            subject: $donation,
            user: $request->user(),
        );

        return redirect()
            ->route('admin.donations.show', $donation)
            ->with('success', 'Ownership transferred successfully. Certificate issued to the donor.');
    }
}
