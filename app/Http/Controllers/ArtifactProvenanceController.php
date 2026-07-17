<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveArtifactProvenanceRequest;
use App\Models\Artifact;
use App\Models\ArtifactProvenance;
use App\Services\ActivityLogger;
use App\Services\ArtifactProvenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArtifactProvenanceController extends Controller
{
    public function __construct(
        private ArtifactProvenanceService $provenanceService,
        private ActivityLogger            $activityLogger,
    ) {}

    public function store(SaveArtifactProvenanceRequest $request, Artifact $artifact): JsonResponse
    {
        Gate::authorize('manageProvenance', $artifact);

        $record = $this->provenanceService->addRecord($artifact, $request->user(), $request->validated());

        $this->activityLogger->log(
            action: 'artifact.provenance-added',
            description: "{$request->user()->name} added provenance record \"{$record->title}\" to \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json([
            'success' => true,
            'record'  => $this->recordToArray($record),
        ]);
    }

    public function update(SaveArtifactProvenanceRequest $request, Artifact $artifact, ArtifactProvenance $provenance): JsonResponse
    {
        Gate::authorize('manageProvenance', $artifact);

        $record = $this->provenanceService->updateRecord($provenance, $request->validated());

        $this->activityLogger->log(
            action: 'artifact.provenance-updated',
            description: "{$request->user()->name} updated provenance record \"{$record->title}\" on \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json([
            'success' => true,
            'record'  => $this->recordToArray($record),
        ]);
    }

    public function destroy(Artifact $artifact, ArtifactProvenance $provenance): JsonResponse
    {
        Gate::authorize('manageProvenance', $artifact);

        $this->provenanceService->deleteRecord($provenance);

        $this->activityLogger->log(
            action: 'artifact.provenance-deleted',
            description: auth()->user()->name . " deleted a provenance record from \"{$artifact->name}\".",
            subject: $artifact,
            user: auth()->user(),
        );

        return response()->json(['success' => true]);
    }

    /**
     * Persist a new display order for all provenance records of an artifact.
     * Expects JSON: { "ids": [3, 1, 2] }
     */
    public function reorder(Request $request, Artifact $artifact): JsonResponse
    {
        Gate::authorize('manageProvenance', $artifact);

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->provenanceService->reorder($artifact, $validated['ids']);

        return response()->json(['success' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function recordToArray(ArtifactProvenance $record): array
    {
        return [
            'id'          => $record->id,
            'type'        => $record->type,
            'type_label'  => $record->typeLabel(),
            'type_icon'   => $record->typeIcon(),
            'title'       => $record->title,
            'description' => $record->description,
            'date'        => $record->date?->format('Y-m-d'),
            'date_human'  => $record->date?->format('M j, Y'),
            'location'    => $record->location,
            'source_url'  => $record->source_url,
            'sort_order'  => $record->sort_order,
        ];
    }
}
