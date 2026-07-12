/**
 * Crossfades the hero's background photos on a timer and keeps the dot
 * indicators in sync. Pauses while the user's pointer is over the hero
 * (so it doesn't fight someone trying to read the headline) and never
 * auto-advances under prefers-reduced-motion — the first slide is shown
 * and that's it, since motion here is decorative, not informational.
 */
export function initHeroSlider() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const slides = Array.from(hero.querySelectorAll('.av-hero__slide'));
    const dots = Array.from(hero.querySelectorAll('.av-hero__dot'));
    if (slides.length < 2) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    let current = 0;
    let timer = null;
    const intervalMs = 6000;

    function goTo(index) {
        slides[current].classList.remove('is-active');
        dots[current]?.classList.remove('is-active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('is-active');
        dots[current]?.classList.add('is-active');
    }

    function start() {
        stop();
        timer = window.setInterval(() => goTo(current + 1), intervalMs);
    }

    function stop() {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goTo(index);
            start();
        });
    });

    hero.addEventListener('mouseenter', stop);
    hero.addEventListener('mouseleave', start);

    start();
}
