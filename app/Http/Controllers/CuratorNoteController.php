<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCuratorNoteRequest;
use App\Http\Requests\UpdateCuratorNoteRequest;
use App\Models\Artifact;
use App\Models\CuratorNote;
use App\Services\ActivityLogger;
use App\Services\CuratorNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CuratorNoteController extends Controller
{
    public function __construct(
        private CuratorNoteService $noteService,
        private ActivityLogger     $activityLogger,
    ) {}

    public function store(StoreCuratorNoteRequest $request, Artifact $artifact): JsonResponse
    {
        Gate::authorize('manageCuratorNotes', $artifact);

        $note = $this->noteService->addNote($artifact, $request->user(), $request->validated());

        $this->activityLogger->log(
            action: 'artifact.note-added',
            description: "{$request->user()->name} added a {$note->typeLabel()} note to \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json(['success' => true, 'note' => $this->noteToArray($note)]);
    }

    public function update(UpdateCuratorNoteRequest $request, Artifact $artifact, CuratorNote $note): JsonResponse
    {
        Gate::authorize('manageCuratorNotes', $artifact);

        $note = $this->noteService->updateNote($note, $request->validated());

        $this->activityLogger->log(
            action: 'artifact.note-updated',
            description: "{$request->user()->name} updated a curator note on \"{$artifact->name}\".",
            subject: $artifact,
            user: $request->user(),
        );

        return response()->json(['success' => true, 'note' => $this->noteToArray($note)]);
    }

    public function destroy(Artifact $artifact, CuratorNote $note): JsonResponse
    {
        Gate::authorize('manageCuratorNotes', $artifact);

        $this->noteService->deleteNote($note);

        $this->activityLogger->log(
            action: 'artifact.note-deleted',
            description: auth()->user()->name . " deleted a curator note from \"{$artifact->name}\".",
            subject: $artifact,
            user: auth()->user(),
        );

        return response()->json(['success' => true]);
    }

    /**
     * Toggle pin state on a note.
     */
    public function pin(Artifact $artifact, CuratorNote $note): JsonResponse
    {
        Gate::authorize('manageCuratorNotes', $artifact);

        $note = $this->noteService->togglePin($note);

        return response()->json(['success' => true, 'is_pinned' => $note->is_pinned]);
    }

    /** @return array<string, mixed> */
    private function noteToArray(CuratorNote $note): array
    {
        return [
            'id'           => $note->id,
            'note_type'    => $note->note_type,
            'type_label'   => $note->typeLabel(),
            'type_color'   => $note->typeColor(),
            'border_color' => $note->typeBorderColor(),
            'body'         => $note->body,
            'is_pinned'    => $note->is_pinned,
            'author'       => $note->author?->name ?? 'Unknown',
            'created_at'   => $note->created_at->diffForHumans(),
        ];
    }
}
