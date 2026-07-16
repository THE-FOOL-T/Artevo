/**
 * Reveals elements marked [data-reveal] as they enter the viewport, and
 * drives the animated stat counters marked [data-counter]. Both respect
 * prefers-reduced-motion by skipping straight to the final state.
 */

let _revealObserver = null;
const _prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initScrollReveal() {
    _setupObserver();
    _observeAll(document);
}

/**
 * Call this after injecting new HTML into the DOM (e.g. AJAX results)
 * so that newly-added [data-reveal] elements get observed too.
 */
export function revealNewElements(root = document) {
    if (_prefersReduced || !('IntersectionObserver' in window)) {
        root.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
        root.querySelectorAll('[data-counter]').forEach((el) => runCounter(el));
        return;
    }
    _observeAll(root);
}

function _setupObserver() {
    if (_revealObserver) return; // already set up

    if (_prefersReduced || !('IntersectionObserver' in window)) {
        // Skip animations — just show everything immediately.
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
        document.querySelectorAll('[data-counter]').forEach((el) => runCounter(el));
        return;
    }

    _revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 }); // lowered from 0.15 so above-fold items trigger

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                runCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    // Store counter observer on the module-level for use in _observeAll
    _revealObserver._counterObs = counterObserver;
}

function _observeAll(root) {
    if (!_revealObserver) return;
    root.querySelectorAll('[data-reveal]:not(.is-visible)').forEach((el) => _revealObserver.observe(el));
    if (_revealObserver._counterObs) {
        root.querySelectorAll('[data-counter]').forEach((el) => _revealObserver._counterObs.observe(el));
    }
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
