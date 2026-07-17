<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveArtifactImageRequest;
use App\Models\Artifact;
use App\Models\ArtifactImage;
use App\Services\ArtifactMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ArtifactImageController extends Controller
{
    public function __construct(private ArtifactMediaService $mediaService)
    {
    }

    public function store(SaveArtifactImageRequest $request, Artifact $artifact): RedirectResponse
    {
        $this->mediaService->addImages($artifact, $request->file('images'), $request->validated('caption'));

        return back()->with('success', 'Images added.');
    }

    public function destroy(Artifact $artifact, ArtifactImage $image): RedirectResponse
    {
        Gate::authorize('manageMedia', $artifact);

        abort_unless($image->artifact_id === $artifact->id, 404);

        $wasPrimary = $image->is_primary;
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        // If the cover image was removed, promote the next one so the
        // artifact never silently ends up without a thumbnail.
        if ($wasPrimary) {
            $artifact->images()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function makePrimary(Artifact $artifact, ArtifactImage $image): RedirectResponse
    {
        Gate::authorize('manageMedia', $artifact);

        abort_unless($image->artifact_id === $artifact->id, 404);

        $artifact->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Cover image updated.');
    }
}
