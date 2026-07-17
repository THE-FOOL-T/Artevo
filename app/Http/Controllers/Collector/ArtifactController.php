<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtifactRequest;
use App\Http\Requests\UpdateArtifactRequest;
use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Services\ActivityLogger;
use App\Services\ArtifactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    public function __construct(
        private ArtifactService $artifactService,
        private ActivityLogger $activityLogger,
    ) {
    }

    public function index(Request $request): View
    {
        return view('collector.artifacts.index', [
            'artifacts' => $request->user()->collectedArtifacts()->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Artifact::class);

        return view('collector.artifacts.create', [
            'categories' => ArtifactCategory::orderBy('name')->get(),
            'materials' => ArtifactMaterial::orderBy('name')->get(),
        ]);
    }

    public function store(StoreArtifactRequest $request): RedirectResponse
    {
        $artifact = $this->artifactService->createForCollector(
            $request->user(),
            $request->safe()->except('tags'),
            $request->validated('tags', []),
        );

        $this->activityLogger->log(
            action: 'artifact.created',
            description: "{$request->user()->name} added the artifact \"{$artifact->name}\" to their collection.",
            subject: $artifact,
            user: $request->user(),
        );

        return redirect()->route('collector.artifacts.edit', $artifact)
            ->with('success', 'Artifact created. Add images and documents below.');
    }

    public function edit(Artifact $artifact): View
    {
        Gate::authorize('update', $artifact);

        return view('collector.artifacts.edit', [
            'artifact' => $artifact->load(['images', 'documents', 'tags']),
            'categories' => ArtifactCategory::orderBy('name')->get(),
            'materials' => ArtifactMaterial::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArtifactRequest $request, Artifact $artifact): RedirectResponse
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

    public function destroy(Request $request, Artifact $artifact): RedirectResponse
    {
        Gate::authorize('delete', $artifact);

        $name = $artifact->name;
        $artifact->delete();

        $this->activityLogger->log(
            action: 'artifact.deleted',
            description: "{$request->user()->name} deleted the artifact \"{$name}\" from their collection.",
            user: $request->user(),
        );

        return redirect()->route('collector.artifacts.index')->with('success', "\"{$name}\" was deleted.");
    }
}
