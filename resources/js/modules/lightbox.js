/**
 * Powers [data-gallery] thumbnail grids: clicking any [data-gallery-item]
 * opens a shared fullscreen lightbox (built once, reused for every
 * gallery on the page) with prev/next navigation, Escape/backdrop-click
 * to close, and an optional download link for authenticated users.
 */
export function initLightbox() {
    const galleries = document.querySelectorAll('[data-gallery]');
    if (galleries.length === 0) return;

    const backdrop = document.createElement('div');
    backdrop.className = 'av-lightbox-backdrop';
    const isAuthenticated = document.body.dataset.authenticated === '1';
    backdrop.innerHTML = `
        <button type="button" class="av-lightbox__close" aria-label="Close">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <button type="button" class="av-lightbox__prev" aria-label="Previous image">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <img class="av-lightbox__image" src="" alt="">
        <button type="button" class="av-lightbox__next" aria-label="Next image">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
        <span class="av-lightbox__counter"></span>
        ${isAuthenticated ? '<a class="av-lightbox__download av-btn av-btn--ghost" download target="_blank" rel="noopener">Download</a>' : ''}
    `;
    document.body.appendChild(backdrop);

    const image = backdrop.querySelector('.av-lightbox__image');
    const counter = backdrop.querySelector('.av-lightbox__counter');
    const downloadLink = backdrop.querySelector('.av-lightbox__download');

    let activeItems = [];
    let activeIndex = 0;

    function show(index) {
        activeIndex = (index + activeItems.length) % activeItems.length;
        const item = activeItems[activeIndex];
        const fullUrl = item.dataset.galleryFull || item.querySelector('img')?.src || '';
        image.src = fullUrl;
        image.alt = item.querySelector('img')?.alt || '';
        counter.textContent = `${activeIndex + 1} / ${activeItems.length}`;
        if (downloadLink) downloadLink.href = fullUrl;
    }

    function open(gallery, index) {
        activeItems = Array.from(gallery.querySelectorAll('[data-gallery-item]'));
        if (activeItems.length === 0) return;
        show(index);
        backdrop.classList.add('is-open');
    }

    function close() {
        backdrop.classList.remove('is-open');
    }

    galleries.forEach((gallery) => {
        gallery.querySelectorAll('[data-gallery-item]').forEach((item, index) => {
            item.addEventListener('click', () => open(gallery, index));
        });
    });

    backdrop.querySelector('.av-lightbox__close').addEventListener('click', close);
    backdrop.querySelector('.av-lightbox__prev').addEventListener('click', () => show(activeIndex - 1));
    backdrop.querySelector('.av-lightbox__next').addEventListener('click', () => show(activeIndex + 1));

    backdrop.addEventListener('click', (event) => {
        if (event.target === backdrop) close();
    });

    document.addEventListener('keydown', (event) => {
        if (!backdrop.classList.contains('is-open')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') show(activeIndex - 1);
        if (event.key === 'ArrowRight') show(activeIndex + 1);
    });
}
