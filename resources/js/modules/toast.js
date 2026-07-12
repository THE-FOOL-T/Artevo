/**
 * Lightweight toast notifications. A single region is created once and
 * reused for the life of the page. Other modules (and inline scripts in
 * later phases — e.g. auction outbid alerts) call window.avToast(...).
 */
export function initToast() {
    let region = document.querySelector('.av-toast-region');
    if (!region) {
        region = document.createElement('div');
        region.className = 'av-toast-region';
        region.setAttribute('aria-live', 'polite');
        document.body.appendChild(region);
    }

    window.avToast = function avToast(message, type = 'success', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `av-toast av-toast--${type}`;
        toast.textContent = message;
        region.appendChild(toast);

        window.setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 200ms ease-out';
            window.setTimeout(() => toast.remove(), 200);
        }, duration);
    };

    // Surface Laravel session flash messages (set in the layout as a data
    // attribute) as a toast on page load, so the pattern is consistent
    // whether a message came from a redirect or a future AJAX call.
    const flash = document.querySelector('[data-flash]');
    if (flash) {
        const message = flash.dataset.flash;
        const type = flash.dataset.flashType || 'success';
        if (message) {
            window.avToast(message, type);
        }
    }
}
