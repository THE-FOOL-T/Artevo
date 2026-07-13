/**
 * Reveals elements marked [data-reveal] as they enter the viewport, and
 * drives the animated stat counters marked [data-counter]. Both respect
 * prefers-reduced-motion by skipping straight to the final state.
 */
export function initScrollReveal() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealTargets = document.querySelectorAll('[data-reveal]');
    const counterTargets = document.querySelectorAll('[data-counter]');

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        revealTargets.forEach((el) => el.classList.add('is-visible'));
        counterTargets.forEach((el) => runCounter(el));
        return;
    }

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealTargets.forEach((el) => revealObserver.observe(el));

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                runCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    counterTargets.forEach((el) => counterObserver.observe(el));
}

/**
 * Animates a number from 0 to the value in data-counter over ~1.2s using
 * an eased step function, then writes the exact final value so rounding
 * never leaves a slightly-wrong number on screen.
 */
function runCounter(el) {
    const target = parseInt(el.dataset.counter, 10) || 0;
    const suffix = el.dataset.counterSuffix || '';
    const duration = 1200;
    const start = performance.now();

    function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(eased * target).toLocaleString() + suffix;
        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    }
    requestAnimationFrame(tick);
}
