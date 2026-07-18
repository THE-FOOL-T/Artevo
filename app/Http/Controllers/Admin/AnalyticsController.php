<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Auction;
use App\Models\Donation;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index()
    {
        Gate::authorize('access-analytics');

        $metrics = [
            'total_users' => User::count(),
            'total_museums' => Museum::count(),
            'verified_museums' => Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)->count(),
            'total_artifacts' => Artifact::count(),
            'public_artifacts' => Artifact::where('visibility', 'public')->count(),
            'active_auctions' => Auction::where('status', 'active')->count(),
            'pending_donations' => Donation::where('status', 'pending')->count(),
        ];

        // Recent platform growth (e.g. users registered in last 30 days)
        $newUsersThisMonth = User::where('created_at', '>=', now()->subDays(30))->count();
        $newArtifactsThisMonth = Artifact::where('created_at', '>=', now()->subDays(30))->count();

        return view('admin.analytics.index', compact('metrics', 'newUsersThisMonth', 'newArtifactsThisMonth'));
    }
}
