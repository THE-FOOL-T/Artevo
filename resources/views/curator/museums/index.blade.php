@extends('layouts.app')

@section('title', 'My Museums — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <div class="flex-between">
                <div>
                    <x-tag>{{ auth()->user()->isAdmin() ? 'All Museums' : 'My Museums' }}</x-tag>
                    <h1 style="margin-top: var(--space-4);">{{ auth()->user()->isAdmin() ? 'Every museum on Artevo' : 'Your museum profiles' }}</h1>
                </div>
                <x-button href="{{ route('curator.museums.create') }}" variant="primary">New museum</x-button>
            </div>

            @if ($museums->isEmpty())
                <x-card class="mt-8">
                    <p style="margin: 0;">No museum profiles yet. Create your first one to get started.</p>
                </x-card>
            @else
                <div class="grid grid-3" style="margin-top: var(--space-8);">
                    @foreach ($museums as $museum)
                        <x-card class="av-card--media">
                            <img src="{{ $museum->coverImageUrl() ?? 'https://picsum.photos/seed/museum-' . $museum->id . '/480/300' }}" alt="" class="av-card--media__image">
                            <div class="av-card--media__body">
                                @if ($museum->featured)
                                    <x-tag variant="success" class="av-tag--pill">Featured</x-tag>
                                @endif
                                <x-museum-verification-badge :museum="$museum" />
                                <h3 class="mt-4">{{ $museum->name }}</h3>
                                <p>{{ $museum->city }}{{ $museum->city && $museum->country ? ', ' : '' }}{{ $museum->country }}</p>
                                <div class="flex gap-3">
                                    <a href="{{ route('curator.museums.edit', $museum) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Edit &rarr;</a>
                                    <a href="{{ route('curator.museums.dashboard', $museum) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Dashboard</a>
                                    <a href="{{ route('museums.show', $museum) }}" style="color: var(--ink-700); font-weight: 600; font-size: var(--text-sm);">Public page</a>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-6);">
                    {{ $museums->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
