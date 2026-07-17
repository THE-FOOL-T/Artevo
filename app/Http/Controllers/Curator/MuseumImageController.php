<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curator\StoreMuseumImageRequest;
use App\Models\Museum;
use App\Models\MuseumImage;
use App\Services\MuseumMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MuseumImageController extends Controller
{
    public function __construct(private MuseumMediaService $mediaService)
    {
    }

    public function store(StoreMuseumImageRequest $request, Museum $museum): RedirectResponse
    {
        $this->mediaService->addGalleryImages($museum, $request->file('images'), $request->validated('caption'));

        return back()->with('success', 'Gallery images added.');
    }

    public function destroy(Museum $museum, MuseumImage $image): RedirectResponse
    {
        Gate::authorize('manageMedia', $museum);

        abort_unless($image->museum_id === $museum->id, 404);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }
}
