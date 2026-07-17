@extends('layouts.app')

@section('title', 'Explore Museums — Artevo')
@section('meta_description', 'Browse partner museums on Artevo — profiles, collections, and contact details for cultural institutions preserving history.')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>Directory</x-tag>
            <h1 style="margin-top: var(--space-4);">Explore <em>partner museums</em></h1>
            <p style="max-width: 560px;">Institutions preserving and exhibiting history on Artevo.</p>

            <form method="GET" action="{{ route('museums.index') }}" style="margin-top: var(--space-6); max-width: 420px;">
                <div class="av-field" style="margin-bottom: 0;">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, city or country">
                </div>
            </form>

            @if ($museums->isEmpty())
                <x-card class="mt-8">
                    <p style="margin: 0;">No museums match your search yet.</p>
                </x-card>
            @else
                <div class="grid grid-3" style="margin-top: var(--space-8);">
                    @foreach ($museums as $museum)
                        <x-card class="av-card--media" data-reveal>
                            <img src="{{ $museum->coverImageUrl() ?? 'https://picsum.photos/seed/museum-' . $museum->id . '/480/300' }}" alt="{{ $museum->name }}" class="av-card--media__image">
                            <div class="av-card--media__body">
                            <div class="flex gap-2">
                                @if ($museum->featured)
                                    <x-tag variant="success" class="av-tag--pill">Featured</x-tag>
                                @endif
                                <x-museum-verification-badge :museum="$museum" />
                            </div>
                                <h3 class="mt-4">{{ $museum->name }}</h3>
                                @if ($museum->tagline)
                                    <p>{{ $museum->tagline }}</p>
                                @endif
                                <p style="font-size: var(--text-sm); color: var(--stone-600);">{{ $museum->city }}{{ $museum->city && $museum->country ? ', ' : '' }}{{ $museum->country }}</p>
                                <a href="{{ route('museums.show', $museum) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View profile &rarr;</a>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-8);">
                    {{ $museums->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
