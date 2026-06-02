# Abilities Inspection — 2026-06-02

## Purpose

Record a **read-only** inspection of the live `wickedevolutions` site performed through the WordPress Abilities MCP (`mcp__wordpress__*`), capturing theme/menu/pattern/asset state and any Abilities product gaps. This run changed **no** WordPress state — it was inquiry-only.

## Scope

- Site key: `wickedevolutions` (`https://wickedevolutions.com`, site name *The Mirror Experiment*).
- Bridge: connected; `get_started` reported WP `7.0`, user `J System Test` (administrator), 257 abilities enabled.
- Mode: knowledge boot directive places the site in read-only/inquiry mode unless lifted. The user authorized this orchestration test; the inspection deliberately stayed read-only. No content, settings, themes, menus, or media were mutated.

## Ability calls

| Ability / tool | Result |
|----------------|--------|
| `knowledge/boot` | Success — read-only directive confirmed |
| `knowledge/get` (doc_type=site-state, slug=current) | Success — prior diagnostic state retrieved |
| Discover abilities (themes / menus / patterns / blocks) | Success — names confirmed valid |
| `mcp_adapter_batch_execute` (5 ability names) | **Failed** — every name returned `Tool not found: ...` |
| `themes/get-active` | Success |
| `themes/list-mods` | Success |
| `themes/design-snapshot` | Success |
| `menus/list-locations` | Success |
| `patterns/list` | Success |
| `themes/list-enqueued-assets` | Success |

## Successful findings

- **Active theme** — `Wicked Evolutions` (slug `wickedevolutions-theme`), version `0.3.1`, block theme `true`, text domain `wickedevolutions`, requires WP `6.7`, requires PHP `8.0`.
- **Theme mods** — 10 mods, including nav menu locations `topbar=31` / `tabstrip=32`, `custom_css_post_id=-1`, `we_enable_blog_templates=1`, `we_enable_blog_css=1`, `we_default_color_scheme=dark`, `we_body_class=main`.
- **Design snapshot** — returned palette / typography / spacing / layout / templates; `custom_css` empty; 12 `wp_template` entries.
- **Menu locations** — `topbar` and `tabstrip` assigned; `footer` and `sidebar` locations unassigned.
- **Patterns** — 23 patterns total; first page includes core query/nav patterns plus the wicked sidebar/tabstrip hidden patterns.
- **Enqueued assets** — one rule, `the-mirror-landing-page`, src `assets/css/landing-page.css`, condition template `page-landing`, added by `ability`.

## Product gap

One Abilities gap observed and logged in [`abilities-gap-log.md`](abilities-gap-log.md):

- `mcp_adapter_batch_execute` returned `Tool not found: ...` for all five ability names, even though the same names were confirmed valid via discovery and each executed successfully through individual `mcp_adapter_execute_ability` calls. The batch path cannot resolve abilities that individual execution can. **Status: Open.** Fallback used: individual `mcp_adapter_execute_ability` calls.

## Site/theme findings

> These are site/theme observations, **not** Abilities gaps.

- **Missing landing CSS file** — the enqueued-assets rule `the-mirror-landing-page` points at `assets/css/landing-page.css` (condition: template `page-landing`, added by `ability`), but `file_exists=false`. The asset is registered/enqueued while the underlying file is absent, so the landing template would load a missing stylesheet. Worth reconciling the enqueue rule against the theme's actual asset files.
- **Unassigned menu locations** — `footer` and `sidebar` menu locations have no menu assigned.

## Next recommended phase

- Reconcile the `the-mirror-landing-page` enqueue rule with the theme source (add the missing `assets/css/landing-page.css` or remove/repoint the rule).
- File the `mcp_adapter_batch_execute` resolution gap with the Abilities MCP maintainers using the logged row.
- If the user lifts read-only mode, follow up on the open items from prior site-state (plugin update check — no `plugins/check-updates` ability exists — Redis recommendation, test taxonomies/menus, header navigation, custom patterns).
