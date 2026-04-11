# Wicked Evolutions Theme — Configuration

The theme adapts to whatever WordPress install it runs on. There is no hardcoded knowledge of the WE multisite network in the theme code. Each capability the theme provides is opt-in via a `theme_mod`. A fresh install with zero configuration renders as a normal WordPress site with the default WE design system.

## Fresh-Install Contract

When this theme is installed and activated on a brand-new WordPress site with no theme modifications set, you get:

- Standard WordPress single-post and category templates (no blog-styled overrides)
- Standard WordPress front page (no knowledge-styled override)
- Theme CSS (`assets/css/theme.css`) — the design system
- Theme toggle script (always loaded — dark/light switch in the topbar)
- Dark color scheme as the default (`<html data-default-theme="dark">`)
- No site-specific body class
- No style variation force-merge (the theme's default `theme.json` applies)
- No blog-specific CSS
- No table-of-contents script

This is what `abilitiesforai.io` and any third-party install gets out of the box.

## Capabilities

Each capability is independently toggleable via `set_theme_mod()` (or the Customizer, or `wp_options.theme_mods_wickedevolutions-theme`, or the `themes-set-mod` ability).

| theme_mod | Type | Default | Effect when set |
|---|---|---|---|
| `we_enable_blog_templates` | bool | `false` | Routes single posts to `single-post-blog` template and category archives to `category-blog` template (the WE blog design with sidebar). |
| `we_enable_blog_css` | bool | `false` | Enqueues `assets/css/blog.css` after `assets/css/theme.css`. |
| `we_enable_toc` | bool | `false` | Enqueues `assets/js/toc.js` for in-article table-of-contents rails. |
| `we_style_variation` | string | `''` | Force-merges `styles/{value}.json` on top of `theme.json` via the `wp_theme_json_data_theme` filter. Empty string = no force-merge. The named file must exist in the theme's `styles/` directory. |
| `we_enable_knowledge_frontpage` | bool | `false` | Routes the front page to the `front-page-knowledge` template. |
| `we_default_color_scheme` | string | `'dark'` | Sets `<html data-default-theme="..."`> so the theme-toggle script applies the right scheme before first paint. Values: `'dark'`, `'light'`. Empty string suppresses the attribute entirely. |
| `we_body_class` | string | `''` | Adds `we-site-{value}` to `<body class="...">` for site-specific CSS scoping. The value is passed through `sanitize_html_class()`. Empty string emits no class. |

## Setting Capabilities

### Via the abilities API

```
mcp__wordpress__themes-set-mod  site=<site>  key=we_enable_blog_templates  value=true
mcp__wordpress__themes-set-mod  site=<site>  key=we_body_class             value=knowledge
mcp__wordpress__themes-set-mod  site=<site>  key=we_default_color_scheme   value=light
```

### Via WP-CLI

```
wp theme mod set we_enable_blog_templates 1
wp theme mod set we_body_class knowledge
wp theme mod set we_default_color_scheme light
```

### Via a mu-plugin (for installs that want capabilities baked in)

```php
add_action( 'after_setup_theme', function () {
    set_theme_mod( 'we_enable_blog_templates', true );
    set_theme_mod( 'we_body_class', 'knowledge' );
    set_theme_mod( 'we_default_color_scheme', 'light' );
} );
```

## Wicked Evolutions Multisite Configuration

The 4 WE subsites use these capability sets to reproduce their pre-`0.3.0` behavior. These are the values that were seeded as part of the `0.3.0` upgrade.

### `wickedevolutions.com` (main blog)

```
we_enable_blog_templates       = true
we_enable_blog_css             = true
we_enable_toc                  = false
we_style_variation             = ''
we_enable_knowledge_frontpage  = false
we_default_color_scheme        = 'dark'
we_body_class                  = 'main'
```

### `community.wickedevolutions.com`

```
we_enable_blog_templates       = false
we_enable_blog_css             = false
we_enable_toc                  = true
we_style_variation             = ''
we_enable_knowledge_frontpage  = false
we_default_color_scheme        = 'dark'
we_body_class                  = 'community'
```

### `knowledge.wickedevolutions.com`

```
we_enable_blog_templates       = false
we_enable_blog_css             = false
we_enable_toc                  = true
we_style_variation             = 'knowledge'
we_enable_knowledge_frontpage  = true
we_default_color_scheme        = 'light'
we_body_class                  = 'knowledge'
```

### `test1.wickedevolutions.com`

```
we_enable_blog_templates       = false
we_enable_blog_css             = false
we_enable_toc                  = true
we_style_variation             = ''
we_enable_knowledge_frontpage  = false
we_default_color_scheme        = 'dark'
we_body_class                  = 'test1'
```

## Body Class CSS Scoping

When `we_body_class` is set, the theme emits `we-site-{value}` on `<body>`. CSS that needs to target a specific install can scope itself like this:

```css
.we-site-knowledge .wp-block-post-content p {
    font-family: var(--wp--preset--font-family--spectral);
}
```

Pre-`0.3.0` installs used `.subsite-{blog_id}` for this. The new convention is name-based, not identity-based.

## Defaults Rationale

Every default is chosen so that an unconfigured install behaves like normal WordPress:

- **`we_enable_blog_templates = false`** — the blog templates are an opinionated WE design (sidebar, related posts, dense list view). A new install probably doesn't want them.
- **`we_enable_blog_css = false`** — the blog CSS only matters if blog templates are also on.
- **`we_enable_toc = false`** — the TOC script targets a specific in-article rail markup. A new install without that markup doesn't need the script.
- **`we_style_variation = ''`** — force-merging a variation overrides the theme's default `theme.json`. A new install should get the default design system, not someone else's variation.
- **`we_enable_knowledge_frontpage = false`** — the knowledge front-page template assumes a documentation-style information architecture. A new install probably has its own front page.
- **`we_default_color_scheme = 'dark'`** — the WE design system is dark-first. The theme-toggle script lets users switch.
- **`we_body_class = ''`** — emitting nothing is the right zero-config behavior. A class scoped to nothing is invisible.
