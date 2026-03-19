/**
 * Wicked Evolutions — Dark/Light theme toggle
 * Uses data-theme attribute on <html>. Dark is default.
 * Pattern: Design System v1.3 §2.5
 */
function weToggleTheme() {
    var html = document.documentElement;
    var isDark = html.getAttribute('data-theme') !== 'light';
    var next = isDark ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('we-theme', next);
}

// Restore saved preference before first paint
(function () {
    var saved = localStorage.getItem('we-theme');
    if (saved === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
})();
