/**
 * Adds a Material-style ripple to any element with the .av-btn class on
 * click, purely as a tactile hover/press cue — it never blocks the click
 * handler or form submission it's attached to.
 */
export function initRipple() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.av-btn');
        if (!button) return;

        const rect = button.getBoundingClientRect();
        const ripple = document.createElement('span');
        const size = Math.max(rect.width, rect.height);

        ripple.className = 'av-btn__ripple';
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
        ripple.style.top = `${event.clientY - rect.top - size / 2}px`;

        button.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
    });
}
