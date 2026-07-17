<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArtifactRequest;
use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Models\Museum;
use App\Services\ActivityLogger;
use App\Services\ArtifactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    public function __construct(
        private ArtifactService $artifactService,
        private ActivityLogger $activityLogger,
    ) {
    }

    /**
     * A museum's artifacts. Gate::authorize('update', $museum) doubles
     * as "can manage this museum's collection at all" — the same rule
     * that lets a curator edit the museum profile.
     */
    public function index(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        return view('curator.artifacts.index', [
            'museum' => $museum,
            'artifacts' => $museum->artifacts()->latest()->paginate(12),
        ]);
    }

    public function create(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        return view('curator.artifacts.create', [
            'museum' => $museum,
            'categories' => ArtifactCategory::orderBy('name')->get(),
            'materials' => ArtifactMaterial::orderBy('name')->get(),
        ]);
    }

    public function store(SaveArtifactRequest $request, Museum $museum): RedirectResponse
    {
        Gate::authorize('update', $museum);

        $artifact = $this->artifactService->createForMuseum(
            $museum,
            $request->user(),
            $request->safe()->except('tags'),
            $request->validated('tags', []),
        );

        $this->activityLogger->log(
            action: 'artifact.created',
            description: "{$request->user()->name} added the artifact \"{$artifact->name}\" to \"{$museum->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return redirect()->route('curator.museums.artifacts.edit', [$museum, $artifact])
            ->with('success', 'Artifact created. Add images and documents below.');
    }

    public function edit(Museum $museum, Artifact $artifact): View
    {
        Gate::authorize('update', $artifact);

        return view('curator.artifacts.edit', [
            'museum' => $museum,
            'artifact' => $artifact->load(['images', 'documents', 'tags', 'provenance', 'curatorNotes.author', 'restorationRecords']),
            'categories' => ArtifactCategory::orderBy('name')->get(),
            'materials' => ArtifactMaterial::orderBy('name')->get(),
        ]);
    }

    public function update(SaveArtifactRequest $request, Museum $museum, Artifact $artifact): RedirectResponse
    {
        $this->artifactService->update(
            $artifact,
            $request->safe()->except('tags'),
            $request->validated('tags', []),
        );

        $this->activityLogger->log(
            action: 'artifact.updated',
            description: "{$request->user()->name} updated the artifact \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return back()->with('success', 'Artifact updated.');
    }

    public function destroy(Museum $museum, Artifact $artifact): RedirectResponse
    {
        Gate::authorize('delete', $artifact);

        $name = $artifact->name;
        $artifact->delete();

        $this->activityLogger->log(
            action: 'artifact.deleted',
            description: auth()->user()->name . " deleted the artifact \"{$name}\" from \"{$museum->name}\".",
            user: auth()->user(),
        );

        return redirect()->route('curator.museums.artifacts.index', $museum)->with('success', "\"{$name}\" was deleted.");
    }
}
