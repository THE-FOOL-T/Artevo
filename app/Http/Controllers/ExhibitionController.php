<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExhibitionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Exhibition::published()
            ->with(['museum', 'sections'])
            ->withCount('sections')
            ->latest('starts_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('museum', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->boolean('active_now')) {
            $now = now();
            $query->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
        }

        $exhibitions = $query->paginate(12)->withQueryString();

        $featured = Exhibition::published()
            ->featured()
            ->with('museum')
            ->withCount('sections')
            ->latest()
            ->take(4)
            ->get();

        return view('exhibitions.index', [
            'exhibitions' => $exhibitions,
            'featured'    => $featured,
        ]);
    }

    public function show(Exhibition $exhibition): View
    {
        if (! $exhibition->isPublished()) {
            abort(404);
        }

        $exhibition->incrementViews();

        $exhibition->load([
            'museum',
            'sections.artifacts.images',
        ]);

        $related = Exhibition::published()
            ->where('museum_id', $exhibition->museum_id)
            ->where('id', '!=', $exhibition->id)
            ->with('museum')
            ->withCount('sections')
            ->take(3)
            ->get();

        return view('exhibitions.show', [
            'exhibition' => $exhibition,
            'related'    => $related,
        ]);
    }
}
