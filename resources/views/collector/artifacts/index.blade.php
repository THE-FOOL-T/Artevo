@extends('layouts.app')

@section('title', 'My Collection — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>My Collection</x-tag>
            <div class="flex-between" style="margin-top: var(--space-4);">
                <h1 style="margin-bottom: 0;">Your artifacts</h1>
                <x-button href="{{ route('collector.artifacts.create') }}" variant="primary">Add artifact</x-button>
            </div>

            @if ($artifacts->isEmpty())
                <x-card class="mt-8">
                    <p style="margin: 0;">No artifacts yet. Add the first piece in your collection.</p>
                </x-card>
            @else
                <div class="grid grid-3" style="margin-top: var(--space-8);">
                    @foreach ($artifacts as $artifact)
                        <x-card class="av-card--media">
                            <img src="{{ $artifact->primaryImageUrl() }}" alt="{{ $artifact->name }}" class="av-card--media__image">
                            <div class="av-card--media__body">
                                <x-tag variant="{{ $artifact->status === 'public' ? 'success' : 'muted' }}" class="av-tag--pill">{{ ucfirst($artifact->status) }}</x-tag>
                                <h3 class="mt-4">{{ $artifact->name }}</h3>
                                <p style="font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->artifact_code }}</p>
                                <a href="{{ route('collector.artifacts.edit', $artifact) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Edit &rarr;</a>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-8);">
                    {{ $artifacts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
