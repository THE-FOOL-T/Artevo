<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Models\Museum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    /**
     * Public artifact directory with live filtering.
     *
     * A normal page load renders the full page; a request carrying the
     * X-Requested-With: XMLHttpRequest header (sent by
     * artifact-search.js) gets back just the results-grid partial, so
     * the filter sidebar can update the page without a full reload.
     */
    public function index(Request $request): View
    {
        $artifacts = $this->filteredQuery($request)->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('artifacts.partials.results', ['artifacts' => $artifacts]);
        }

        return view('artifacts.index', [
            'artifacts' => $artifacts,
            'categories' => \Illuminate\Support\Facades\Cache::remember('public_artifact_categories', 86400, fn () => ArtifactCategory::orderBy('name')->get()),
            'materials' => \Illuminate\Support\Facades\Cache::remember('public_artifact_materials', 86400, fn () => ArtifactMaterial::orderBy('name')->get()),
            'civilizations' => \Illuminate\Support\Facades\Cache::remember('public_artifact_civilizations', 86400, fn () => Artifact::query()->public()->whereNotNull('civilization')->distinct()->orderBy('civilization')->pluck('civilization')),
            'countries' => \Illuminate\Support\Facades\Cache::remember('public_artifact_countries', 86400, fn () => Artifact::query()->public()->whereNotNull('country_of_origin')->distinct()->orderBy('country_of_origin')->pluck('country_of_origin')),
            'museums' => \Illuminate\Support\Facades\Cache::remember('public_artifact_museums', 86400, fn () => Museum::query()->whereHas('artifacts', fn ($q) => $q->public())->orderBy('name')->get()),
        ]);
    }

    /**
     * The shared filter query, used by both the full page and the AJAX
     * partial so the two can never drift out of sync with each other.
     */
    private function filteredQuery(Request $request)
    {
        return Artifact::query()
            ->public()
            ->with(['images', 'category', 'museum'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('civilization', 'like', "%{$search}%")
                    ->orWhere('country_of_origin', 'like', "%{$search}%"));
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->filled('material'), fn ($query) => $query->where('material_id', $request->integer('material')))
            ->when($request->filled('civilization'), fn ($query) => $query->where('civilization', $request->string('civilization')))
            ->when($request->filled('country'), fn ($query) => $query->where('country_of_origin', $request->string('country')))
            ->when($request->filled('museum'), fn ($query) => $query->where('museum_id', $request->integer('museum')))
            ->when($request->input('sort') === 'name', fn ($query) => $query->orderBy('name'))
            ->when($request->input('sort') === 'value', fn ($query) => $query->orderByDesc('estimated_value'))
            ->when(! $request->filled('sort') || $request->input('sort') === 'newest', fn ($query) => $query->latest());
    }

    /**
     * Public artifact detail page: hero gallery with lightbox, a simple
     * honest timeline (just what Artevo itself knows — created/updated —
     * since provenance/restoration/exhibition history don't exist as
     * data until Phase 10-15), and related artifacts by shared category,
     * civilization, or tags.
     */
    public function show(Artifact $artifact): View
    {
        Gate::authorize('view', $artifact);

        $artifact->load(['images', 'documents', 'tags', 'category', 'material', 'museum', 'collector', 'provenance.recorder', 'restorationRecords', 'activeAuction', 'qrCode', 'certificates']);


        return view('artifacts.show', [
            'artifact' => $artifact,
            'relatedArtifacts' => $this->relatedTo($artifact),
        ]);
    }

    /**
     * Other public artifacts sharing a category, civilization, or tag —
     * ranked by how many of those they share, most-matching first.
     */
    private function relatedTo(Artifact $artifact)
    {
        $tagIds = $artifact->tags->pluck('id');

        return Artifact::query()
            ->public()
            ->whereKeyNot($artifact->id)
            ->with(['images', 'category'])
            ->where(function ($query) use ($artifact, $tagIds) {
                $query->where('category_id', $artifact->category_id)
                    ->orWhere('civilization', $artifact->civilization)
                    ->orWhereHas('tags', fn ($q) => $q->whereIn('artifact_tags.id', $tagIds));
            })
            ->limit(4)
            ->get();
    }
}
