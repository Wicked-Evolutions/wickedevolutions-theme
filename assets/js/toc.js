/**
 * Table of Contents — auto-generation from headings.
 * Scans post content for h2/h3 elements, builds a nav list,
 * and injects it into .we-toc-rail if present.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var tocContainer = document.querySelector('.we-toc-rail');
        if (!tocContainer) return;

        var content = document.querySelector('.entry-content, .wp-block-post-content');
        if (!content) return;

        var headings = content.querySelectorAll('h2, h3');
        if (headings.length < 2) return;

        var list = document.createElement('ul');
        list.className = 'we-toc-list';

        headings.forEach(function (heading, index) {
            if (!heading.id) {
                heading.id = 'toc-' + index;
            }
            var li = document.createElement('li');
            li.className = 'we-toc-item we-toc-' + heading.tagName.toLowerCase();
            var a = document.createElement('a');
            a.href = '#' + heading.id;
            a.textContent = heading.textContent;
            li.appendChild(a);
            list.appendChild(li);
        });

        tocContainer.appendChild(list);
    });
})();
