<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Exhibition;
use App\Models\Museum;
use Illuminate\View\View;

/**
 * Renders the static marketing pages that don't yet need a dedicated
 * module of their own. As Artevo grows, this stays limited to purely
 * informational pages — anything backed by a database table gets its
 * own controller instead.
 */
class PageController extends Controller
{
    public function home(): View
    {
        // Live platform stats
        $stats = [
            'artifacts' => Artifact::count(),
            'museums'   => Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)->count(),
            'verified'  => Artifact::where('verification_status', 'verified')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];

        // Featured exhibitions strip (up to 3)
        $featuredExhibitions = Exhibition::published()
            ->featured()
            ->with('museum')
            ->withCount('sections')
            ->latest()
            ->take(3)
            ->get();

        // Featured museums (up to 4 verified, featured first)
        $featuredMuseums = Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)
            ->withCount(['artifacts', 'exhibitions' => fn ($q) => $q->published()])
            ->orderByDesc('featured')
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        return view('home', compact('stats', 'featuredExhibitions', 'featuredMuseums'));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}

