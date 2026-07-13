@props(['user', 'size' => null]) {{-- size: null (38px) | 'lg' (88px) --}}

@php
    $sizeClass = $size === 'lg' ? ' av-avatar--lg' : '';
@endphp

@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}'s avatar" {{ $attributes->merge(['class' => 'av-avatar' . $sizeClass]) }}>
@else
    <div {{ $attributes->merge(['class' => 'av-avatar av-avatar--initials' . $sizeClass]) }} aria-hidden="true">
        {{ $user->initials() }}
    </div>
@endif
