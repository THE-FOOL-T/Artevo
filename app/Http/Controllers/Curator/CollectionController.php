<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Collection;
use App\Models\Museum;
use App\Services\ActivityLogger;
use App\Services\CollectionMediaService;
use App\Services\CollectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function __construct(
        private CollectionService $collectionService,
        private CollectionMediaService $collectionMediaService,
        private ActivityLogger $activityLogger,
    ) {}

    public function index(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        $collections = $museum->collections()
            ->withCount('artifacts')
            ->latest()
            ->paginate(12);

        return view('curator.collections.index', [
            'museum'      => $museum,
            'collections' => $collections,
        ]);
    }

    public function create(Museum $museum): View
    {
        Gate::authorize('update', $museum);

        return view('curator.collections.create', ['museum' => $museum]);
    }

    public function store(StoreCollectionRequest $request, Museum $museum): RedirectResponse
    {
        Gate::authorize('update', $museum);

        $data = $request->safe()->except('cover_image');

        $collection = $this->collectionService->createForMuseum($museum, $request->user(), $data);

        if ($request->hasFile('cover_image')) {
            $path = $this->collectionMediaService->replaceCoverImage($collection, $request->file('cover_image'));
            $collection->update(['cover_image_path' => $path]);
        }

        $this->activityLogger->log(
            action: 'collection.created',
            description: "{$request->user()->name} created the collection \"{$collection->name}\" in \"{$museum->name}\".",
            subject: $collection,
            user: $request->user(),
        );

        return redirect()->route('curator.collections.edit', [$museum, $collection])
            ->with('success', 'Collection created. Add artifacts below.');
    }

    public function edit(Museum $museum, Collection $collection): View
    {
        Gate::authorize('update', $collection);

        $artifacts = $museum->artifacts()
            ->with('images')
            ->public()
            ->get();

        $collectionArtifacts = $collection->artifacts()
            ->with('images')
            ->get();

        return view('curator.collections.edit', [
            'museum'              => $museum,
            'collection'          => $collection,
            'artifacts'           => $artifacts,
            'collectionArtifacts' => $collectionArtifacts,
        ]);
    }

    public function update(UpdateCollectionRequest $request, Museum $museum, Collection $collection): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image', 'remove_cover_image']);

        if ($request->validated('remove_cover_image')) {
            $this->collectionMediaService->deleteCoverImage($collection);
        } elseif ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $this->collectionMediaService->replaceCoverImage(
                $collection,
                $request->file('cover_image')
            );
        }

        $this->collectionService->update($collection, $data);

        $this->activityLogger->log(
            action: 'collection.updated',
            description: "{$request->user()->name} updated the collection \"{$collection->name}\".",
            subject: $collection,
            user: $request->user(),
        );

        return back()->with('success', 'Collection updated.');
    }

    public function destroy(Museum $museum, Collection $collection): RedirectResponse
    {
        Gate::authorize('delete', $collection);

        $name = $collection->name;
        $collection->delete();

        $this->activityLogger->log(
            action: 'collection.deleted',
            description: auth()->user()->name . " deleted the collection \"{$name}\" from \"{$museum->name}\".",
            user: auth()->user(),
        );

        return redirect()->route('curator.collections.index', $museum)
            ->with('success', "\"{$name}\" was deleted.");
    }
}
