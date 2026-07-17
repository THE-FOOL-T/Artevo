<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\CuratorNote;
use App\Models\User;

class CuratorNoteService
{
    /**
     * Add a private note to an artifact.
     *
     * @param  array<string, mixed>  $data  Validated from StoreCuratorNoteRequest
     */
    public function addNote(Artifact $artifact, User $author, array $data): CuratorNote
    {
        return $artifact->curatorNotes()->create([
            ...$data,
            'author_id' => $author->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateNote(CuratorNote $note, array $data): CuratorNote
    {
        $note->update($data);

        return $note->fresh();
    }

    public function deleteNote(CuratorNote $note): void
    {
        $note->delete();
    }

    /**
     * Toggle the pinned state of a note.
     * Pinned notes sort to the top of the list.
     */
    public function togglePin(CuratorNote $note): CuratorNote
    {
        $note->update(['is_pinned' => ! $note->is_pinned]);

        return $note->fresh();
    }
}
