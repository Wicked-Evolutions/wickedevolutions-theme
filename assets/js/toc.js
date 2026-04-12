/**
 * Table of Contents — scroll-spy + mobile toggle.
 * Reads server-rendered .we-toc-list links. No heading discovery or list generation.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var nav = document.querySelector('.we-toc-nav');
        if (!nav) return;

        var links = nav.querySelectorAll('.we-toc-list a');
        if (!links.length) return;

        // Build heading-to-link map (skip "Back to top" which links to #).
        var entries = [];
        links.forEach(function (link) {
            var id = link.getAttribute('href');
            if (!id || id === '#') return;
            var target = document.getElementById(id.slice(1));
            if (target) {
                entries.push({ el: target, link: link });
            }
        });

        if (!entries.length) return;

        // Scroll-spy via IntersectionObserver.
        var activeLink = null;
        var headingStates = new Map();

        function setActive(link) {
            if (activeLink === link) return;
            if (activeLink) activeLink.classList.remove('active');
            if (link) {
                link.classList.add('active');
                // Auto-scroll TOC rail so active item is visible.
                var rail = link.closest('.we-toc-rail, .we-toc-nav');
                if (rail && rail.scrollHeight > rail.clientHeight) {
                    link.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            }
            activeLink = link;
        }

        var observer = new IntersectionObserver(function (observed) {
            observed.forEach(function (entry) {
                headingStates.set(entry.target, entry.isIntersecting);
            });

            // Find the last heading that has scrolled past the top.
            var current = null;
            for (var i = 0; i < entries.length; i++) {
                var rect = entries[i].el.getBoundingClientRect();
                if (rect.top <= 150) {
                    current = entries[i].link;
                }
            }
            if (current) {
                setActive(current);
            } else if (entries.length) {
                setActive(entries[0].link);
            }
        }, {
            rootMargin: '-112px 0px -60% 0px',
            threshold: [0, 1]
        });

        entries.forEach(function (entry) {
            observer.observe(entry.el);
        });

        // Fallback scroll listener for edge cases (fast scrolling past all observers).
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                var current = null;
                for (var i = 0; i < entries.length; i++) {
                    if (entries[i].el.getBoundingClientRect().top <= 150) {
                        current = entries[i].link;
                    }
                }
                if (current) setActive(current);
                ticking = false;
            });
        }, { passive: true });

        // Mobile toggle button.
        var toggle = nav.querySelector('.we-toc-toggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!expanded));
                nav.classList.toggle('we-toc-open', !expanded);
            });
        }
    });
})();
