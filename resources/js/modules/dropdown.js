/**
 * Powers every [data-dropdown] on the page (currently just the navbar's
 * authenticated user menu). Multiple dropdowns can exist; opening one
 * closes any other that's open.
 */
export function initDropdown() {
    const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));
    if (dropdowns.length === 0) return;

    function closeAll(except = null) {
        dropdowns.forEach((dropdown) => {
            if (dropdown === except) return;
            dropdown.querySelector('[data-dropdown-panel]')?.classList.remove('is-open');
            dropdown.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    }

    dropdowns.forEach((dropdown) => {
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');
        const panel = dropdown.querySelector('[data-dropdown-panel]');
        if (!trigger || !panel) return;

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = panel.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', String(isOpen));
            if (isOpen) closeAll(dropdown);
        });
    });

    document.addEventListener('click', () => closeAll());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });
}
