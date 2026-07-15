<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleUpgradeController extends Controller
{
    /**
     * Upgrade the current Visitor to a Collector. Restricted to visitors
     * by the 'visitor' middleware on the route — a collector, curator or
     * admin hitting this action would already fail that check.
     *
     * Administrator and Curator are never self-service; those are
     * assigned by an existing administrator (Admin\UserController).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->update(['role' => User::ROLE_COLLECTOR]);

        return redirect()->route('dashboard')
            ->with('success', "You're now a Collector — you can upload artifacts, join auctions, and donate pieces once those modules launch.");
    }
}
