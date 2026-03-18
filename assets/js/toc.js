/**
 * Table of Contents — auto-generation from headings.
 * Scans post content for h2/h3 elements, builds a nav list,
 * injects it into .we-toc-rail, and tracks scroll position.
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
        var links = [];

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
            links.push({ el: heading, link: a });
        });

        tocContainer.appendChild(list);

        // Scroll-spy: highlight the nearest heading
        var ticking = false;
        function onScroll() {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                var scrollPos = window.scrollY + 140;
                var current = null;
                for (var i = 0; i < links.length; i++) {
                    if (links[i].el.offsetTop <= scrollPos) {
                        current = links[i];
                    }
                }
                links.forEach(function (item) {
                    item.link.classList.remove('active');
                });
                if (current) {
                    current.link.classList.add('active');
                }
                ticking = false;
            });
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });
})();
