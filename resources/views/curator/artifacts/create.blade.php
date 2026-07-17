@extends('layouts.app')

@section('title', "Add Artifact to {$museum->name} — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>{{ $museum->name }}</x-tag>
            <h1 style="margin-top: var(--space-4);">Add an artifact</h1>

            <x-card class="mt-6">
                <form method="POST" action="{{ route('curator.museums.artifacts.store', $museum) }}" novalidate>
                    @csrf
                    @include('artifacts.partials.form', ['artifact' => null])
                    <x-button type="submit" variant="primary" class="mt-4">Create artifact</x-button>
                </form>
            </x-card>
        </div>
    </section>
@endsection
