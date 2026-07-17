@extends('layouts.app')

@section('title', 'Create a Museum Profile — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>New Museum</x-tag>
            <h1 style="margin-top: var(--space-4);">Create a museum profile</h1>
            <p>You can add gallery images and contact details once the profile is created.</p>

            <x-card class="mt-6">
                <form method="POST" action="{{ route('curator.museums.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @include('curator.museums.partials.form', ['museum' => null])
                    <x-button type="submit" variant="primary" class="mt-4">Create museum</x-button>
                </form>
            </x-card>
        </div>
    </section>
@endsection
