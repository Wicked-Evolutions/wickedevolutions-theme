# Changelog

## 0.3.0

### Changed
- Theme is now install-aware. All multisite-specific behavior moved from hardcoded `blog_id` checks in `functions.php` to a capability model driven by 7 `theme_mod` settings, each with a zero-config default that produces a normal WordPress site on a fresh install. See `THEME-CONFIG.md` for the full capability contract.
- `assets/css/theme.css`: renamed selector `.subsite-4` to `.we-site-knowledge` to match the new install-aware body class scheme.

### Removed
- Deleted dead header template parts: `parts/header-main.html`, `parts/header-knowledge.html`, `parts/header-community.html`. All four header parts were byte-identical to `parts/header.html`; the `render_block_data` filter that switched between them is also removed.
- Removed the corresponding `customTemplates` entries from `theme.json`.
- All `blog_id` and `get_current_blog_id()` references are gone from theme code.

### Added
- `THEME-CONFIG.md` documenting the 7 capability `theme_mod` settings, defaults, and how to set them via abilities, WP-CLI, or `set_theme_mod()`.

## 0.2.0

- Earlier development. See git history.
