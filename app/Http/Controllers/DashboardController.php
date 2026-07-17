<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Museum;
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
                'user' => $user,
                'totalUsers' => User::count(),
                'roleCounts' => User::query()
                    ->selectRaw('role, count(*) as total')
                    ->groupBy('role')
                    ->pluck('total', 'role'),
                'totalMuseums' => Museum::count(),
                'totalArtifacts' => Artifact::count(),
            ]);
        }

        if ($user->isCurator()) {
            $museumIds = Museum::where('curator_id', $user->id)->pluck('id');

            return view('dashboards.curator', [
                'user' => $user,
                'museumCount' => $museumIds->count(),
                'artifactCount' => Artifact::whereIn('museum_id', $museumIds)->count(),
            ]);
        }

        if ($user->isCollector()) {
            return view('dashboards.collector', [
                'user' => $user,
                'artifactCount' => Artifact::where('collector_id', $user->id)->count(),
            ]);
        }

        return view('dashboards.visitor', ['user' => $user]);
    }
}
