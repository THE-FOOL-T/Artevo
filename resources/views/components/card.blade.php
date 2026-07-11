@props(['eyebrow' => null])

<div {{ $attributes->merge(['class' => 'av-card']) }}>
    @if ($eyebrow)
        <span class="av-card__eyebrow">{{ $eyebrow }}</span>
    @endif
    {{ $slot }}
</div>
