/**
 * Any element with data-modal-open="id" opens the matching
 * [data-modal="id"] backdrop; data-modal-close (or clicking the
 * backdrop itself, or Escape) closes it.
 */
export function initModal() {
    const modals = Array.from(document.querySelectorAll('[data-modal]'));
    if (modals.length === 0) return;

    function open(id) {
        const modal = modals.find((m) => m.dataset.modal === id);
        modal?.classList.add('is-open');
        modal?.querySelector('input, button, textarea')?.focus();
    }

    function closeAll() {
        modals.forEach((modal) => modal.classList.remove('is-open'));
    }

    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => open(trigger.dataset.modalOpen));
    });

    document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
        trigger.addEventListener('click', closeAll);
    });

    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeAll();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });
}
