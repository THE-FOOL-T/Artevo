/**
 * Powers the artifact directory's filter sidebar. Any change to a
 * filter control re-fetches just the results partial (text search is
 * debounced), swaps it into the results container, and updates the
 * browser URL via pushState so filters stay shareable/bookmarkable and
 * the back button works.
 */
import { revealNewElements } from './scroll-reveal.js';

export function initArtifactSearch() {
    const form = document.querySelector('[data-artifact-search-form]');
    const results = document.querySelector('[data-artifact-results]');
    if (!form || !results) return;

    let debounceTimer = null;

    function fetchResults(url, pushState = true) {
        results.classList.add('is-loading');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => response.text())
            .then((html) => {
                results.innerHTML = html;
                results.classList.remove('is-loading');
                // Re-observe any [data-reveal] elements injected by the AJAX swap.
                revealNewElements(results);
                if (pushState) {
                    window.history.pushState({ url }, '', url);
                }
            })
            .catch(() => {
                results.classList.remove('is-loading');
            });
    }

    function currentUrl() {
        const params = new URLSearchParams(new FormData(form));
        // Drop empty values so the URL stays clean (no ?search=&category=).
        Array.from(params.keys()).forEach((key) => {
            if (!params.get(key)) params.delete(key);
        });
        const query = params.toString();
        return form.action + (query ? `?${query}` : '');
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        fetchResults(currentUrl());
    });

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => fetchResults(currentUrl()));
    });

    const searchInput = form.querySelector('[data-artifact-search-input]');
    searchInput?.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => fetchResults(currentUrl()), 350);
    });

    // Re-fetch when user navigates back/forward.
    // Use the saved URL from pushState state object to get the correct page.
    window.addEventListener('popstate', (e) => {
        fetchResults(e.state?.url || window.location.href, false);
    });

    // Sync the filter form fields to match the current URL params
    // so the sidebar reflects the state after a popstate navigation.
    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(window.location.search);
        form.querySelectorAll('select, input').forEach((el) => {
            if (el.name) {
                el.value = params.get(el.name) || '';
            }
        });
    });

    // Pagination links live inside the results partial and are replaced
    // on every fetch, so this listens on the container (event
    // delegation) rather than on the links directly.
    results.addEventListener('click', (event) => {
        const link = event.target.closest('nav[aria-label="Pagination"] a');
        if (!link) return;
        event.preventDefault();
        fetchResults(link.href);
        window.scrollTo({ top: results.offsetTop - 100, behavior: 'smooth' });
    });
}
