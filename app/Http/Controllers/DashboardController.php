<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard for the authenticated user's role.
     *
     * Most metrics on these views are still skeletons — everything that
     * depends on modules from later phases (museums, artifacts, auctions,
     * verifications...) is labeled with when it's coming rather than
     * faked. The admin dashboard is the exception: user/role counts are
     * real, since the User model already exists.
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
            ]);
        }

        $view = match (true) {
            $user->isCurator() => 'dashboards.curator',
            $user->isCollector() => 'dashboards.collector',
            default => 'dashboards.visitor',
        };

        return view($view, ['user' => $user]);
    }
}
