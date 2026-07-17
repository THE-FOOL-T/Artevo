<?php

namespace App\Http\Controllers;

use App\Models\Museum;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MuseumController extends Controller
{
    /**
     * Public museum directory. Featured museums first, then a simple
     * name/city/country/verification search. Passes artifact and
     * exhibition counts for each museum card.
     */
    public function index(Request $request): View
    {
        $museums = Museum::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%"));
            })
            ->when($request->boolean('verified'), fn ($q) => $q->where('verification_status', Museum::VERIFICATION_VERIFIED))
            ->when($request->boolean('featured'), fn ($q) => $q->where('featured', true))
            ->when($request->filled('country'), fn ($q) => $q->where('country', $request->string('country')))
            ->withCount(['artifacts', 'exhibitions' => fn ($q) => $q->published()])
            ->orderByDesc('featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $countries = \Illuminate\Support\Facades\Cache::remember('public_museum_countries', 86400, fn () => 
            Museum::whereNotNull('country')
                ->distinct()
                ->orderBy('country')
                ->pluck('country')
        );

        // ── Phase 15: All museums with coordinates for the map tab ───────────
        // We load ALL museums (not just the current page) so the map is complete
        // even when the grid is paginated or filtered.
        $museumsGeoJson = \Illuminate\Support\Facades\Cache::remember('public_museum_geojson', 86400, fn () => 
            Museum::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get(['id', 'name', 'slug', 'city', 'country', 'latitude', 'longitude'])
                ->map(fn ($m) => [
                    'lat'  => (float) $m->latitude,
                    'lng'  => (float) $m->longitude,
                    'name' => $m->name,
                    'url'  => route('museums.show', $m),
                    'city' => trim(($m->city ?? '') . ', ' . ($m->country ?? ''), ', '),
                ])
                ->values()
        );

        return view('museums.index', [
            'museums'          => $museums,
            'countries'        => $countries,
            'museumsGeoJson'   => $museumsGeoJson->toJson(),
            'hasMapData'       => $museumsGeoJson->isNotEmpty(),
        ]);
    }


    /**
     * Public museum profile page. Every visit bumps the view counter
     * that feeds the curator's per-museum dashboard (Phase 6) — a known
     * simplification is that this doesn't deduplicate the owner's own
     * visits or filter bots, which is fine for this stage.
     */
    public function show(Museum $museum): View
    {
        $museum->load(['images', 'contacts']);
        $museum->incrementViews();

        $artifacts = $museum->artifacts()
            ->with(['category', 'images'])
            ->where('verification_status', 'verified')
            ->latest()
            ->take(6)
            ->get();

        $collections = $museum->collections()
            ->where('is_public', true)
            ->latest()
            ->take(4)
            ->get();

        $exhibitions = $museum->exhibitions()
            ->where('status', 'published')
            ->latest()
            ->take(4)
            ->get();

        return view('museums.show', [
            'museum'      => $museum,
            'artifacts'   => $artifacts,
            'collections' => $collections,
            'exhibitions' => $exhibitions,
        ]);
    }
}
