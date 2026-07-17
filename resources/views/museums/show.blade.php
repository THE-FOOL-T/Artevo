@extends('layouts.app')

@section('title', "{$museum->name} — Artevo")
@section('meta_description', $museum->tagline ?? "Explore {$museum->name} on Artevo.")

@section('content')

    <section style="position: relative; height: 320px; overflow: hidden;">
        <img src="{{ $museum->coverImageUrl() ?? 'https://picsum.photos/seed/museum-' . $museum->id . '/1600/500' }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(21,18,13,0.75), rgba(21,18,13,0.1));"></div>
    </section>

    <section class="av-section av-section--white" style="padding-top: var(--space-8);">
        <div class="container">
            <div class="flex gap-4" style="align-items: flex-start; margin-top: -80px; margin-bottom: var(--space-6);">
                <img src="{{ $museum->logoUrl() ?? 'https://picsum.photos/seed/museum-logo-' . $museum->id . '/120/120' }}" alt="{{ $museum->name }} logo" style="width: 96px; height: 96px; border-radius: var(--radius-md); border: 4px solid var(--white); object-fit: cover; box-shadow: var(--shadow-md);">
                <div style="padding-top: 88px;">
                    <div class="flex gap-2">
                        @if ($museum->featured)
                            <x-tag variant="success" class="av-tag--pill">Featured</x-tag>
                        @endif
                        <x-museum-verification-badge :museum="$museum" />
                    </div>
                    <h1 style="margin-top: var(--space-2); margin-bottom: 0;">{{ $museum->name }}</h1>
                    @if ($museum->tagline)
                        <p>{{ $museum->tagline }}</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-2" style="gap: var(--space-10); align-items: start;">
                <div>
                    @if ($museum->description)
                        <h3>About</h3>
                        <p style="white-space: pre-line;">{{ $museum->description }}</p>
                    @endif

                    @if ($museum->images->isNotEmpty())
                        <h3 class="mt-8">Gallery</h3>
                        <div class="grid grid-3" style="gap: var(--space-3);">
                            @foreach ($museum->images as $image)
                                <img src="{{ $image->url() }}" alt="{{ $image->caption ?? $museum->name }}" style="width: 100%; height: 120px; object-fit: cover; border-radius: var(--radius-sm); transition: transform var(--duration-base) var(--ease-out);" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <x-card>
                        <span class="av-card__eyebrow">Details</span>
                        <dl style="margin: 0; font-size: var(--text-sm);">
                            @if ($museum->foundation_year)
                                <dt style="color: var(--stone-600);">Founded</dt>
                                <dd style="margin: 0 0 var(--space-3);">{{ $museum->foundation_year }}</dd>
                            @endif
                            @if ($museum->address || $museum->city)
                                <dt style="color: var(--stone-600);">Location</dt>
                                <dd style="margin: 0 0 var(--space-3);">
                                    {{ implode(', ', array_filter([$museum->address, $museum->city, $museum->country])) }}
                                    @if ($museum->directionsUrl())
                                        <br><a href="{{ $museum->directionsUrl() }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600;">Get directions &rarr;</a>
                                    @endif
                                </dd>
                            @endif
                            @if ($museum->website)
                                <dt style="color: var(--stone-600);">Website</dt>
                                <dd style="margin: 0 0 var(--space-3);"><a href="{{ $museum->website }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600;">{{ $museum->website }}</a></dd>
                            @endif
                        </dl>

                        @if (! empty(array_filter($museum->social_links ?? [])))
                            <div class="flex gap-3" style="margin-top: var(--space-3);">
                                @foreach (($museum->social_links ?? []) as $platform => $url)
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="av-footer__social" style="border-color: var(--color-border);" aria-label="{{ ucfirst($platform) }}">{{ strtoupper(substr($platform, 0, 2)) }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </x-card>

                    @if ($museum->opening_hours)
                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Opening hours</span>
                            <table style="width: 100%; font-size: var(--text-sm);">
                                @foreach ($museum->opening_hours as $day => $hours)
                                    @if ($hours)
                                        <tr>
                                            <td style="padding: 4px 0; text-transform: capitalize; color: var(--stone-600);">{{ $day }}</td>
                                            <td style="padding: 4px 0; text-align: right;">{{ $hours }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </x-card>
                    @endif

                    @if ($museum->contacts->isNotEmpty())
                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Contact</span>
                            @foreach ($museum->contacts as $contact)
                                <div class="mt-2">
                                    <strong style="font-size: var(--text-sm);">{{ $contact->label }}</strong>
                                    <p style="margin: 0; font-size: var(--text-sm);">
                                        {{ $contact->email }}@if($contact->email && $contact->phone)<br>@endif{{ $contact->phone }}
                                    </p>
                                </div>
                            @endforeach
                        </x-card>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
