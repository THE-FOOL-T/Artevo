@props(['align' => 'right'])

<div class="av-dropdown" data-dropdown>
    <button type="button" class="av-dropdown__trigger" data-dropdown-trigger aria-haspopup="true" aria-expanded="false">
        {{ $trigger }}
    </button>

    <div class="av-dropdown__panel" data-dropdown-panel role="menu">
        {{ $slot }}
    </div>
</div>
