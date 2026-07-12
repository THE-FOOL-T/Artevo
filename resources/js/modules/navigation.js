/**
 * Navigation behaviour: adds a shadow to the sticky nav once the page has
 * scrolled past the hero, and toggles the mobile menu panel.
 */
export function initNavigation() {
    const nav = document.querySelector('[data-nav]');
    if (!nav) return;

    const toggleBtn = nav.querySelector('[data-nav-toggle]');
    const mobilePanel = nav.querySelector('[data-nav-mobile]');

    // Pages with a full-bleed dark photo hero (currently just the homepage)
    // get a transparent, light-text nav until the user scrolls past it.
    if (document.querySelector('[data-hero]')) {
        nav.classList.add('is-on-dark-hero');
    }

    // Add a subtle shadow once the user scrolls, so the nav reads as
    // "lifted" above the content rather than blending into it.
    const onScroll = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (toggleBtn && mobilePanel) {
        toggleBtn.addEventListener('click', () => {
            const isOpen = mobilePanel.classList.toggle('is-open');
            toggleBtn.setAttribute('aria-expanded', String(isOpen));
        });

        // Close the mobile panel automatically when a link inside it is used.
        mobilePanel.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobilePanel.classList.remove('is-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }
}
