<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtifactDocumentRequest;
use App\Models\Artifact;
use App\Models\ArtifactDocument;
use App\Services\ArtifactMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ArtifactDocumentController extends Controller
{
    public function __construct(private ArtifactMediaService $mediaService)
    {
    }

    public function store(StoreArtifactDocumentRequest $request, Artifact $artifact): RedirectResponse
    {
        $this->mediaService->addDocument(
            $artifact,
            $request->file('document'),
            $request->validated('title'),
            $request->validated('document_type'),
        );

        return back()->with('success', 'Document added.');
    }

    public function destroy(Artifact $artifact, ArtifactDocument $document): RedirectResponse
    {
        Gate::authorize('manageMedia', $artifact);

        abort_unless($document->artifact_id === $artifact->id, 404);

        Storage::disk('public')->delete($document->document_path);
        $document->delete();

        return back()->with('success', 'Document removed.');
    }
}
