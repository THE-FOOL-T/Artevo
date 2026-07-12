/**
 * ARTEVO — MAIN JS ENTRY POINT
 * Loaded once via @vite(['resources/js/app.js']) in layouts/app.blade.php.
 * Every reusable behaviour lives in resources/js/modules/* and is wired up
 * here on DOMContentLoaded — later phases add new modules to this same file
 * rather than scattering inline <script> tags across views.
 */
import { initNavigation } from './modules/navigation.js';
import { initScrollReveal } from './modules/scroll-reveal.js';
import { initToast } from './modules/toast.js';
import { initRipple } from './modules/ripple.js';
import { initHeroSlider } from './modules/hero-slider.js';

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initScrollReveal();
    initToast();
    initRipple();
    initHeroSlider();
});
