@props(['id', 'title', 'open' => false])

<div class="av-modal-backdrop{{ $open ? ' is-open' : '' }}" data-modal="{{ $id }}">
    <div class="av-modal" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <h3 id="{{ $id }}-title">{{ $title }}</h3>

        {{ $slot }}
    </div>
</div>
