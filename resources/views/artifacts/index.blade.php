@extends('layouts.app')

@section('title', 'Explore Artifacts — Artevo')
@section('meta_description', 'Browse verified historical artifacts on Artevo — from ancient civilizations to modern history.')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>Archive</x-tag>
            <h1 style="margin-top: var(--space-4);">Explore the <em>archive</em></h1>
            <p style="max-width: 560px;">Public artifacts from museums and collectors on Artevo. Filters update
                instantly — no page reload.</p>

            <form data-artifact-search-form action="{{ route('artifacts.index') }}" method="GET" style="margin-top: var(--space-8);">
                <div class="av-search-layout">
                    <aside class="av-search-sidebar">
                        <div class="av-field">
                            <label for="search">Search</label>
                            <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Name or description" data-artifact-search-input>
                        </div>
                        <div class="av-field">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-field">
                            <label for="material">Material</label>
                            <select id="material" name="material">
                                <option value="">All materials</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}" @selected(request('material') == $material->id)>{{ $material->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-field">
                            <label for="civilization">Civilization</label>
                            <select id="civilization" name="civilization">
                                <option value="">All civilizations</option>
                                @foreach ($civilizations as $civilization)
                                    <option value="{{ $civilization }}" @selected(request('civilization') === $civilization)>{{ $civilization }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-field">
                            <label for="country">Country of origin</label>
                            <select id="country" name="country">
                                <option value="">All countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-field">
                            <label for="museum">Museum</label>
                            <select id="museum" name="museum">
                                <option value="">All museums &amp; collectors</option>
                                @foreach ($museums as $museum)
                                    <option value="{{ $museum->id }}" @selected(request('museum') == $museum->id)>{{ $museum->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-field">
                            <label for="sort">Sort by</label>
                            <select id="sort" name="sort">
                                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
                                <option value="name" @selected(request('sort') === 'name')>Name A–Z</option>
                                <option value="value" @selected(request('sort') === 'value')>Estimated value</option>
                            </select>
                        </div>

                        <noscript><button type="submit" class="av-btn av-btn--primary av-btn--block">Apply filters</button></noscript>
                        @if (request()->hasAny(['search', 'category', 'material', 'civilization', 'country', 'museum']))
                            <x-button href="{{ route('artifacts.index') }}" variant="outline-dark" class="av-btn--block mt-4">Clear filters</x-button>
                        @endif
                    </aside>

                    <div class="av-search-results" data-artifact-results>
                        @include('artifacts.partials.results')
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
