<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Collection;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Manages the artifact membership of a collection:
 * adding, removing, and drag-and-drop reordering.
 */
class CollectionArtifactController extends Controller
{
    public function __construct(private CollectionService $collectionService) {}

    /**
     * Add an artifact to a collection.
     */
    public function store(Request $request, Collection $collection): JsonResponse|RedirectResponse
    {
        Gate::authorize('manageArtifacts', $collection);

        $request->validate([
            'artifact_id' => ['required', 'exists:artifacts,id'],
        ]);

        $artifact = Artifact::findOrFail($request->artifact_id);

        $this->collectionService->addArtifact($collection, $artifact);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Artifact added to collection.']);
        }

        return back()->with('success', "\"{$artifact->name}\" added to the collection.");
    }

    /**
     * Remove an artifact from a collection.
     */
    public function destroy(Request $request, Collection $collection, Artifact $artifact): JsonResponse|RedirectResponse
    {
        Gate::authorize('manageArtifacts', $collection);

        $this->collectionService->removeArtifact($collection, $artifact);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Artifact removed from collection.']);
        }

        return back()->with('success', "\"{$artifact->name}\" removed from the collection.");
    }

    /**
     * Accept an ordered array of artifact IDs and persist the new sort_order.
     * Called via AJAX when the user completes a drag-and-drop reorder.
     *
     * @example POST /collections/{collection}/artifacts/reorder
     *          { "artifact_ids": [42, 7, 19] }
     */
    public function reorder(Request $request, Collection $collection): JsonResponse
    {
        Gate::authorize('manageArtifacts', $collection);

        $request->validate([
            'artifact_ids'   => ['required', 'array'],
            'artifact_ids.*' => ['integer', 'exists:artifacts,id'],
        ]);

        $this->collectionService->reorderArtifacts($collection, $request->artifact_ids);

        return response()->json(['message' => 'Order saved.']);
    }
}
