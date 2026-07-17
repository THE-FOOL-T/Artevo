<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestorationRecordRequest;
use App\Http\Requests\UpdateRestorationRecordRequest;
use App\Models\Artifact;
use App\Models\RestorationRecord;
use App\Services\ActivityLogger;
use App\Services\RestorationRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RestorationRecordController extends Controller
{
    public function __construct(
        private RestorationRecordService $recordService,
        private ActivityLogger           $activityLogger,
    ) {}

    public function store(StoreRestorationRecordRequest $request, Artifact $artifact): JsonResponse
    {
        Gate::authorize('manageRestorationRecords', $artifact);

        $record = $this->recordService->addRecord($artifact, $request->user(), $request->validated());

        $this->activityLogger->log(
            action: 'artifact.restoration-added',
            description: "{$request->user()->name} added restoration record \"{$record->title}\" to \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json(['success' => true, 'record' => $this->recordToArray($record)]);
    }

    public function update(UpdateRestorationRecordRequest $request, Artifact $artifact, RestorationRecord $record): JsonResponse
    {
        Gate::authorize('manageRestorationRecords', $artifact);

        $record = $this->recordService->updateRecord($record, $request->validated());

        $this->activityLogger->log(
            action: 'artifact.restoration-updated',
            description: "{$request->user()->name} updated restoration record \"{$record->title}\" on \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json(['success' => true, 'record' => $this->recordToArray($record)]);
    }

    public function destroy(Artifact $artifact, RestorationRecord $record): JsonResponse
    {
        Gate::authorize('manageRestorationRecords', $artifact);

        $this->recordService->deleteRecord($record);

        $this->activityLogger->log(
            action: 'artifact.restoration-deleted',
            description: auth()->user()->name . " deleted a restoration record from \"{$artifact->name}\".",
            subject: $artifact,
            user: auth()->user(),
        );

        return response()->json(['success' => true]);
    }

    /**
     * Persist a new sort order for all restoration records.
     * Expects JSON body: { "ids": [3, 1, 2] }
     */
    public function reorder(Request $request, Artifact $artifact): JsonResponse
    {
        Gate::authorize('manageRestorationRecords', $artifact);

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->recordService->reorder($artifact, $validated['ids']);

        return response()->json(['success' => true]);
    }

    /** @return array<string, mixed> */
    private function recordToArray(RestorationRecord $record): array
    {
        return [
            'id'               => $record->id,
            'category'         => $record->category,
            'category_label'   => $record->categoryLabel(),
            'category_icon'    => $record->categoryIcon(),
            'title'            => $record->title,
            'description'      => $record->description,
            'conservator_name' => $record->conservator_name,
            'institution'      => $record->institution,
            'started_at'       => $record->started_at?->format('Y-m-d'),
            'completed_at'     => $record->completed_at?->format('Y-m-d'),
            'duration_label'   => $record->durationLabel(),
            'sort_order'       => $record->sort_order,
        ];
    }
}
