<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curator\SaveMuseumRequest;
use App\Models\Museum;
use App\Services\ActivityLogger;
use App\Services\MuseumMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MuseumController extends Controller
{
    public function __construct(
        private MuseumMediaService $mediaService,
        private ActivityLogger $activityLogger,
    ) {
    }

    /**
     * "My museums" — an admin sees every museum, a curator sees only
     * the ones they created.
     */
    public function index(Request $request): View
    {
        $museums = Museum::query()
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('curator_id', $request->user()->id))
            ->latest()
            ->paginate(12);

        return view('curator.museums.index', ['museums' => $museums]);
    }

    public function create(): View
    {
        Gate::authorize('create', Museum::class);

        return view('curator.museums.create');
    }

    public function store(SaveMuseumRequest $request): RedirectResponse
    {
        $museum = new Museum($request->safe()->except(['logo', 'cover_image', 'featured']));
        $museum->curator_id = $request->user()->id;

        // Only an admin's request can set featured — a curator posting
        // this field (e.g. via a tampered form) is silently ignored
        // rather than erroring, since it's not a security-critical field.
        if ($request->user()->isAdmin()) {
            $museum->featured = $request->boolean('featured');
        }

        $museum->save();

        if ($request->hasFile('logo')) {
            $museum->update(['logo_path' => $this->mediaService->replaceLogo($museum, $request->file('logo'))]);
        }

        if ($request->hasFile('cover_image')) {
            $museum->update(['cover_image_path' => $this->mediaService->replaceCoverImage($museum, $request->file('cover_image'))]);
        }

        $this->activityLogger->log(
            action: 'museum.created',
            description: "{$request->user()->name} created the museum \"{$museum->name}\".",
            subject: $museum,
            user: $request->user(),
        );

        return redirect()->route('curator.museums.edit', $museum)->with('success', 'Museum profile created. Add a gallery and contact details below.');
    }

    public function edit(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        return view('curator.museums.edit', [
            'museum' => $museum->load(['images', 'contacts']),
        ]);
    }

    /**
     * A single museum's own dashboard — distinct from the curator's
     * personal dashboard (Phase 3), which lists all of their museums.
     * Most metrics are still honest skeletons; views/gallery/contacts
     * counts are real, since that data exists as of Phase 5–6.
     */
    public function dashboard(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        return view('curator.museums.dashboard', [
            'museum' => $museum->loadCount(['images', 'contacts', 'artifacts']),
        ]);
    }

    public function update(SaveMuseumRequest $request, Museum $museum): RedirectResponse
    {
        $museum->fill($request->safe()->except(['logo', 'cover_image', 'featured']));

        if ($request->user()->isAdmin()) {
            $museum->featured = $request->boolean('featured');
        }

        if ($request->hasFile('logo')) {
            $museum->logo_path = $this->mediaService->replaceLogo($museum, $request->file('logo'));
        }

        if ($request->hasFile('cover_image')) {
            $museum->cover_image_path = $this->mediaService->replaceCoverImage($museum, $request->file('cover_image'));
        }

        $museum->save();

        $this->activityLogger->log(
            action: 'museum.updated',
            description: "{$request->user()->name} updated the museum \"{$museum->name}\".",
            subject: $museum,
            user: $request->user(),
        );

        return back()->with('success', 'Museum profile updated.');
    }

    public function destroy(Request $request, Museum $museum): RedirectResponse
    {
        Gate::authorize('delete', $museum);

        $name = $museum->name;
        $museum->delete();

        $this->activityLogger->log(
            action: 'museum.deleted',
            description: "{$request->user()->name} deleted the museum \"{$name}\".",
            user: $request->user(),
        );

        return redirect()->route('curator.museums.index')->with('success', "\"{$name}\" was deleted.");
    }
}
