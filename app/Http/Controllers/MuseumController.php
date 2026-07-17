<?php

namespace App\Http\Controllers;

use App\Models\Museum;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MuseumController extends Controller
{
    /**
     * Public museum directory. Featured museums first, then a simple
     * name/city search — the richer AJAX-driven search experience is
     * built for artifacts specifically in Phase 8; this stays a plain
     * server-rendered filter for now.
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
            ->orderByDesc('featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('museums.index', ['museums' => $museums]);
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

        return view('museums.show', ['museum' => $museum]);
    }
}
