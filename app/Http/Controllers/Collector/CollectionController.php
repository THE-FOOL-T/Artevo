<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCollectionRequest;
use App\Models\Collection;
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

    public function index(): View
    {
        $collections = auth()->user()
            ->collections()
            ->withCount('artifacts')
            ->latest()
            ->paginate(12);

        return view('collector.collections.index', [
            'collections' => $collections,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Collection::class);

        return view('collector.collections.create');
    }

    public function store(SaveCollectionRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');

        $collection = $this->collectionService->createForCollector($request->user(), $data);

        if ($request->hasFile('cover_image')) {
            $path = $this->collectionMediaService->replaceCoverImage($collection, $request->file('cover_image'));
            $collection->update(['cover_image_path' => $path]);
        }

        $this->activityLogger->log(
            action: 'collection.created',
            description: "{$request->user()->name} created the personal collection \"{$collection->name}\".",
            subject: $collection,
            user: $request->user(),
        );

        return redirect()->route('collector.collections.edit', $collection)
            ->with('success', 'Collection created. Add artifacts below.');
    }

    public function edit(Collection $collection): View
    {
        Gate::authorize('update', $collection);

        $myArtifacts = auth()->user()
            ->collectedArtifacts()
            ->with('images')
            ->public()
            ->get();

        $collectionArtifacts = $collection->artifacts()->with('images')->get();

        return view('collector.collections.edit', [
            'collection'          => $collection,
            'myArtifacts'         => $myArtifacts,
            'collectionArtifacts' => $collectionArtifacts,
        ]);
    }

    public function update(SaveCollectionRequest $request, Collection $collection): RedirectResponse
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

    public function destroy(Collection $collection): RedirectResponse
    {
        Gate::authorize('delete', $collection);

        $name = $collection->name;
        $collection->delete();

        $this->activityLogger->log(
            action: 'collection.deleted',
            description: auth()->user()->name . " deleted the personal collection \"{$name}\".",
            user: auth()->user(),
        );

        return redirect()->route('collector.collections.index')
            ->with('success', "\"{$name}\" was deleted.");
    }
}
