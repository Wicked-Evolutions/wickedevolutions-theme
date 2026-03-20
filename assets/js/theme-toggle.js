/**
 * Wicked Evolutions — Dark/Light theme toggle
 * Uses data-theme attribute on <html>. Per-subsite default via data-default-theme.
 * Pattern: Design System v1.3 §2.5
 */
function weToggleTheme() {
    var html = document.documentElement;
    var isDark = html.getAttribute('data-theme') !== 'light';
    var next = isDark ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('we-theme', next);
}

// Restore saved preference or apply per-subsite default before first paint
(function () {
    var saved = localStorage.getItem('we-theme');
    var defaultTheme = document.documentElement.getAttribute('data-default-theme') || 'dark';
    var theme = saved || defaultTheme;
    document.documentElement.setAttribute('data-theme', theme);
})();
