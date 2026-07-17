@extends('layouts.app')

@section('title', "Edit {$artifact->name} — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>My Collection</x-tag>
            <h1 style="margin-top: var(--space-4); margin-bottom: var(--space-2);">{{ $artifact->name }}</h1>
            <p style="font-family: var(--font-mono); font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->artifact_code }}</p>
            <p>
                <a href="{{ route('collector.artifacts.index') }}" style="color: var(--brass-700); font-weight: 600;">&larr; Back to your collection</a>
                @if ($artifact->isPublic())
                    &middot; <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600;">View public page &rarr;</a>
                @endif
            </p>

            <x-card class="mt-6">
                <h3>Profile</h3>
                <form method="POST" action="{{ route('collector.artifacts.update', $artifact) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('artifacts.partials.form')
                    <x-button type="submit" variant="primary" class="mt-4">Save changes</x-button>
                </form>
            </x-card>

            @include('artifacts.partials.media')

            <x-card class="mt-6">
                <h3 style="color: var(--red-600);">Delete this artifact</h3>
                <p>This removes the artifact, its gallery, and its documents. This cannot be undone.</p>
                <x-button type="button" variant="outline-dark" data-modal-open="delete-artifact" style="border-color: var(--red-600); color: var(--red-600);">Delete artifact</x-button>

                <x-modal id="delete-artifact" title="Delete this artifact?">
                    <p>This permanently removes "{{ $artifact->name }}" and everything attached to it.</p>
                    <form method="POST" action="{{ route('collector.artifacts.destroy', $artifact) }}">
                        @csrf
                        @method('DELETE')
                        <div class="av-modal__actions">
                            <x-button type="button" variant="outline-dark" data-modal-close>Cancel</x-button>
                            <x-button type="submit" variant="dark" style="background: var(--red-600);">Delete artifact</x-button>
                        </div>
                    </form>
                </x-modal>
            </x-card>
        </div>
    </section>
@endsection
