<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExhibitionRequest;
use App\Http\Requests\UpdateExhibitionRequest;
use App\Models\Exhibition;
use App\Models\Museum;
use App\Notifications\ExhibitionPublished;
use App\Services\ActivityLogger;
use App\Services\ExhibitionMediaService;
use App\Services\ExhibitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExhibitionController extends Controller
{
    public function __construct(
        private ExhibitionService      $exhibitionService,
        private ExhibitionMediaService $exhibitionMediaService,
        private ActivityLogger         $activityLogger,
    ) {}

    public function index(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        $exhibitions = $museum->exhibitions()
            ->withCount('sections')
            ->latest()
            ->paginate(12);

        return view('curator.exhibitions.index', [
            'museum'      => $museum,
            'exhibitions' => $exhibitions,
        ]);
    }

    public function create(Museum $museum): View
    {
        Gate::authorize('update', $museum);
        Gate::authorize('create', Exhibition::class);

        return view('curator.exhibitions.create', [
            'museum' => $museum,
        ]);
    }

    public function store(StoreExhibitionRequest $request, Museum $museum): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');

        $exhibition = $this->exhibitionService->create($museum, $request->user(), $data);

        if ($request->hasFile('cover_image')) {
            $path = $this->exhibitionMediaService->replaceCoverImage($exhibition, $request->file('cover_image'));
            $exhibition->update(['cover_image_path' => $path]);
        }

        $this->activityLogger->log(
            action: 'exhibition.created',
            description: "{$request->user()->name} created exhibition \"{$exhibition->name}\" for {$museum->name}.",
            subject: $exhibition,
            user: $request->user(),
        );

        return redirect()->route('curator.exhibitions.edit', [$museum, $exhibition])
            ->with('success', 'Exhibition created. Add sections below.');
    }

    public function edit(Museum $museum, Exhibition $exhibition): View
    {
        Gate::authorize('update', $exhibition);

        $exhibition->load('sections.artifacts.images');

        $museumArtifacts = $museum->artifacts()
            ->public()
            ->with('images')
            ->get();

        return view('curator.exhibitions.edit', [
            'museum'          => $museum,
            'exhibition'      => $exhibition,
            'museumArtifacts' => $museumArtifacts,
        ]);
    }

    public function update(UpdateExhibitionRequest $request, Museum $museum, Exhibition $exhibition): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'remove_cover_image']);

        if ($request->validated('remove_cover_image')) {
            $this->exhibitionMediaService->deleteCoverImage($exhibition);
        } elseif ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $this->exhibitionMediaService->replaceCoverImage(
                $exhibition,
                $request->file('cover_image')
            );
        }

        $this->exhibitionService->update($exhibition, $data);

        $this->activityLogger->log(
            action: 'exhibition.updated',
            description: "{$request->user()->name} updated exhibition \"{$exhibition->name}\".",
            subject: $exhibition,
            user: $request->user(),
        );

        return back()->with('success', 'Exhibition saved.');
    }

    public function destroy(Museum $museum, Exhibition $exhibition): RedirectResponse
    {
        Gate::authorize('delete', $exhibition);

        $name = $exhibition->name;
        $exhibition->delete();

        $this->activityLogger->log(
            action: 'exhibition.deleted',
            description: auth()->user()->name . " deleted exhibition \"{$name}\".",
            user: auth()->user(),
        );

        return redirect()->route('curator.exhibitions.index', $museum)
            ->with('success', "\"{$name}\" was deleted.");
    }

    /**
     * Publish a draft exhibition, making it visible to the public.
     */
    public function publish(Museum $museum, Exhibition $exhibition): RedirectResponse
    {
        Gate::authorize('publish', $exhibition);

        $exhibition = $this->exhibitionService->publish($exhibition);

        // Notify the creator (in case an admin pressed publish on their behalf)
        if ($exhibition->creator) {
            $exhibition->creator->notify(
                new ExhibitionPublished($exhibition, auth()->user()->name)
            );
        }

        $this->activityLogger->log(
            action: 'exhibition.published',
            description: auth()->user()->name . " published exhibition \"{$exhibition->name}\".",
            subject: $exhibition,
            user: auth()->user(),
        );

        return back()->with('success', "\"{$exhibition->name}\" is now published.");
    }

    /**
     * Archive a published exhibition, removing it from public view.
     */
    public function archive(Museum $museum, Exhibition $exhibition): RedirectResponse
    {
        Gate::authorize('publish', $exhibition);

        $this->exhibitionService->archive($exhibition);

        $this->activityLogger->log(
            action: 'exhibition.archived',
            description: auth()->user()->name . " archived exhibition \"{$exhibition->name}\".",
            subject: $exhibition,
            user: auth()->user(),
        );

        return back()->with('success', "\"{$exhibition->name}\" has been archived.");
    }
}
