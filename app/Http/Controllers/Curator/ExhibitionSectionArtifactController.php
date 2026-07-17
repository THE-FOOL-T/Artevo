<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\ExhibitionSection;
use App\Services\ExhibitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExhibitionSectionArtifactController extends Controller
{
    public function __construct(private ExhibitionService $exhibitionService) {}

    /**
     * Add an artifact to a section.
     */
    public function store(Request $request, ExhibitionSection $section): JsonResponse
    {
        Gate::authorize('update', $section->exhibition);

        $validated = $request->validate([
            'artifact_id' => ['required', 'integer', 'exists:artifacts,id'],
        ]);

        $artifact = Artifact::findOrFail($validated['artifact_id']);

        $this->exhibitionService->addArtifactToSection($section, $artifact);

        // Return artifact data for instant UI update
        $artifact->load('images');

        return response()->json([
            'success'  => true,
            'artifact' => [
                'id'          => $artifact->id,
                'name'        => $artifact->name,
                'slug'        => $artifact->slug,
                'cover_image' => $artifact->primaryImage()?->url ?? null,
            ],
        ]);
    }

    /**
     * Remove an artifact from a section.
     */
    public function destroy(ExhibitionSection $section, Artifact $artifact): JsonResponse
    {
        Gate::authorize('update', $section->exhibition);

        $this->exhibitionService->removeArtifactFromSection($section, $artifact);

        return response()->json(['success' => true]);
    }

    /**
     * Persist artifact display order within a section.
     * Expects JSON body: { "ids": [5, 2, 8] }
     */
    public function reorder(Request $request, ExhibitionSection $section): JsonResponse
    {
        Gate::authorize('update', $section->exhibition);

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->exhibitionService->reorderSectionArtifacts($section, $validated['ids']);

        return response()->json(['success' => true]);
    }
}
