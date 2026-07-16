<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\ArtifactQrCode;
use App\Models\Auction;
use App\Models\Certificate;
use App\Models\Collection;
use App\Models\Donation;
use App\Models\Exhibition;
use App\Models\Museum;
use App\Models\RestorationRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;


class DashboardController extends Controller
{
    /**
     * Show the dashboard for the authenticated user's role.
     *
     * Most metrics on these views are still skeletons — everything that
     * depends on modules from later phases (auctions, verifications...)
     * is labeled with when it's coming rather than faked. Museum and
     * (as of Phase 7) artifact counts are real everywhere they appear.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboards.admin', [
                'user'                 => $user,
                'totalUsers'           => User::count(),
                'roleCounts'           => User::query()
                    ->selectRaw('role, count(*) as total')
                    ->groupBy('role')
                    ->pluck('total', 'role'),
                'totalMuseums'         => Museum::count(),
                'totalArtifacts'       => Artifact::count(),
                'pendingVerifications' => Artifact::where('verification_status', Artifact::VERIFICATION_PENDING)->count(),
                'activeAuctions'       => Auction::active()->count(),
                'pendingDonations'     => Donation::where('status', Donation::STATUS_PENDING)->count(),
                'totalQrScans'         => ArtifactQrCode::sum('scan_count'),
                'totalCertificates'    => Certificate::count(),
            ]);
        }

        if ($user->isCurator()) {
            $museumIds  = Museum::where('curator_id', $user->id)->pluck('id');
            $artifactIds = Artifact::whereIn('museum_id', $museumIds)->pluck('id');

            return view('dashboards.curator', [
                'user'              => $user,
                'museumCount'       => $museumIds->count(),
                'artifactCount'     => $artifactIds->count(),
                'collectionCount'   => Collection::whereIn('museum_id', $museumIds)->count(),
                'exhibitionCount'   => Exhibition::whereIn('museum_id', $museumIds)->count(),
                'auctionCount'      => Auction::active()
                    ->whereHas('artifact', fn ($q) => $q->whereIn('museum_id', $museumIds))
                    ->count(),
                'restorationCount'  => RestorationRecord::whereIn('artifact_id', $artifactIds)->count(),
                'pendingDonations'  => Donation::whereIn('museum_id', $museumIds)
                    ->where('status', Donation::STATUS_PENDING)
                    ->count(),
            ]);
        }

        if ($user->isCollector()) {
            return view('dashboards.collector', [
                'user'            => $user,
                'artifactCount'   => Artifact::where('collector_id', $user->id)->count(),
                'collectionCount' => Collection::where('collector_id', $user->id)->count(),
                'auctionCount'    => Auction::active()
                    ->whereHas('artifact', fn ($q) => $q->where('collector_id', $user->id))
                    ->count(),
                'donationCount'   => Donation::where('donor_id', $user->id)->count(),
                'pendingDonations' => Donation::where('donor_id', $user->id)
                    ->where('status', Donation::STATUS_PENDING)
                    ->count(),
            ]);
        }

        return view('dashboards.visitor', ['user' => $user]);
    }
}
