@extends('layouts.app')

@section('title', 'My Collections — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>My collections</x-tag>
            <div class="flex-between" style="margin-top: var(--space-4);">
                <h1 style="margin-bottom: 0;">Collections</h1>
                <x-button href="{{ route('collector.collections.create') }}" variant="primary">New collection</x-button>
            </div>
            <p><a href="{{ route('dashboard') }}" style="color: var(--brass-700); font-weight: 600;">&larr; Back to dashboard</a></p>

            <x-alert :message="session('success')" type="success" />
            <x-alert :message="session('error')" type="error" />

            @if($collections->isEmpty())
                <x-card class="mt-8">
                    <p style="margin: 0;">No collections yet. Create your first collection to start grouping your artifacts.</p>
                </x-card>
            @else
                <div class="grid grid-3" style="margin-top: var(--space-8);">
                    @foreach($collections as $collection)
                        <x-card class="av-card--media">
                            <div style="background: #1a1a2e; border-radius: var(--radius-md) var(--radius-md) 0 0; overflow: hidden;">
                                @if($collection->coverImageUrl())
                                    <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--stone-400); font-size: 2.5rem;">🗄️</div>
                                @endif
                            </div>
                            <div class="av-card--media__body">
                                <div class="flex gap-2" style="flex-wrap: wrap; margin-bottom: var(--space-2);">
                                    <x-tag variant="{{ $collection->is_public ? 'success' : 'muted' }}" class="av-tag--pill">
                                        {{ $collection->is_public ? 'Public' : 'Private' }}
                                    </x-tag>
                                    @if($collection->is_featured)
                                        <x-tag variant="warning" class="av-tag--pill">Featured</x-tag>
                                    @endif
                                </div>
                                <h3 style="margin: 0 0 var(--space-1);">{{ $collection->name }}</h3>
                                <p style="font-size: var(--text-sm); color: var(--stone-600); margin: 0 0 var(--space-3);">
                                    {{ $collection->artifacts_count }} artifact{{ $collection->artifacts_count !== 1 ? 's' : '' }}
                                </p>
                                <div class="flex gap-4">
                                    <a href="{{ route('collector.collections.edit', $collection) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Edit &rarr;</a>
                                    @if($collection->is_public)
                                        <a href="{{ route('collections.show', $collection) }}" style="color: var(--stone-600); font-size: var(--text-sm);" target="_blank">View public ↗</a>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-8);">
                    {{ $collections->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
