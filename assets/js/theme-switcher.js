/**
 * theme-switcher.js
 * Dashboard is LIGHT MODE ONLY — no dark mode.
 * Clears any saved dark preference and forces light.
 */
(function () {
    // Remove any dark theme that may have been saved before
    localStorage.removeItem('theme');
    // Always ensure light mode
    document.documentElement.removeAttribute('data-theme');
})();
