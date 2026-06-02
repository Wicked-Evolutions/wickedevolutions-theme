# Native Authority Spec — Wicked Evolutions Theme

> **Phase 1 of the v0.4 native foundation** (roadmap §5.1).
> **Status:** inventory / spec — **no code changes**, **not** the refactor.
> **Mode:** repo-source + docs only. No WordPress calls, no live mutation, no deploy
> were made to produce this document; the only live-state facts cited come from the
> already-recorded read-only inspection in `docs/operations/` (no new calls).
> **Date:** 2026-06-02 · **Branch:** `v0.4-native-foundation`
>
> Read against the canonical contract in [`AGENTS.md`](../../AGENTS.md) and the
> [`v0.4-native-core-refactor-roadmap.md`](../plans/v0.4-native-core-refactor-roadmap.md).
> This is the "what currently owns each decision, and what *should*" audit map that
> roadmap phases 2–10 consume. Where a fact could not be confirmed from repo source it
> is marked **needs verification**; nothing here is invented.

---

## 1. Purpose and scope

This document executes **roadmap phase 1 (native authority spec)**: walk the theme
surface-by-surface and record, per decision, **which authority tier owns it today** and
**which tier should own it** under the `AGENTS.md §1` ladder. It is the read-only
"what is broken or misplaced" inventory that every later phase remediates.

**In scope:**

- An evidence-based ownership map of the current `v0.3.1` theme source.
- A decision inventory marking each decision `correct` / `review` / `misplaced` /
  `intentional exception`, with the evidence path and a next action.
- A candidate phase backlog scoped to **v0.4 native foundation only** (roadmap phases
  2–4), with phases 5–10 noted as later v0.5/v0.6 work.

**Out of scope (explicitly):**

- **No source edits.** This spec changes no theme file. It does not promote, delete,
  rename, or refactor anything — it only records what should move and when.
- **No CSS cleanup.** Per roadmap §6, no selector is renamed or removed before its
  owning tier's authority decision is made.
- **No Abilities / runtime mutation.** Live-state reconciliation (e.g. the orphan
  enqueue rule) stays a separately-authorized Abilities operation; this doc only
  references the already-logged findings.
- **No design-system expansion.** No new colors, fonts, spacing, or surfaces are
  proposed; this is a *placement* audit.

The current theme is treated as the source of truth for **intent and visual result**
and as the **subject** of the audit — not as binding precedent for *where* a decision
should live (roadmap §2).

---

## 2. Authority ladder (`AGENTS.md §1`) mapped to this repo

The ladder is copied/summarized from `AGENTS.md §1`; the right column maps each tier to
the **surfaces that actually exist in this repo today**. "Resolve at the first tier that
can own the decision; never reach down to avoid learning the native mechanism above."

| Tier | Authority layer (`AGENTS.md §1`) | Present in this repo as |
|---|---|---|
| 1 | Core / editor UI & saved block attributes | Inline block attrs in `templates/*.html`, `parts/*.html` (e.g. `wp:columns`, `wp:post-content`, `wp:site-title`, per-block `style`/`fontFamily` JSON) |
| 2 | `theme.json` — global settings, presets, custom properties | `theme.json` (v3): palette, fluid type scale, spacing scale, `settings.custom`, element/block `styles` |
| 3 | Global styles & style variations (`styles/`) | `styles/knowledge.json` (one named variation); activation via `we_style_variation` theme_mod + `wp_theme_json_data_theme` filter |
| 4 | Templates & template parts | `templates/*.html` (12), `parts/header.html`, `parts/footer.html`, `parts/sidebar.html`; `theme.json.templateParts` |
| 5 | Patterns & block style variations | `patterns/*.php` (12); all `Categories: hidden`, `Inserter: false`. No registered block *style* variations exist yet (**needs verification**: none found in source) |
| 6 | Dynamic blocks & render callbacks | No registered dynamic blocks. Computed-at-render logic currently lives **inside patterns** (`patterns/toc.php`, `patterns/nav-sidebar-dynamic.php`) — see §3 and §4 |
| 7 | Theme PHP (`functions.php`) | `functions.php`: template-routing filters, enqueues, variation force-merge, `data-default-theme`/`body_class` filters, meta registration, classic admin UI, `register_nav_menus` |
| 8 | Plugins / Abilities | `theme_mod` capability model (read by tier 7, written via Abilities); ability-managed enqueue rules; documented in `docs/operations/` |
| 9 | Custom CSS/JS (`assets/`) | `assets/css/theme.css`, `assets/css/blog.css`, `assets/js/theme-toggle.js`, `assets/js/toc.js`, `assets/fonts/*.woff2` |

**Posture note (evidence):** `theme.json` sets `appearanceTools: true`,
`useRootPaddingAwareAlignments: true`, `defaultPalette/defaultGradients/defaultFontSizes/
defaultSpacingSizes: false`, and `add_editor_style('assets/css/theme.css')`
(`functions.php:300`) — i.e. the theme is genuinely native-first and editor-styled. The
audit's job is not to rescue a non-native theme but to prove each decision sits at its
correct tier.

---

## 3. Current ownership map by file / surface

### 3.1 `theme.json` (tier 2)

Owns the design system core, and does so well:

- **Palette** — 31 colors: a dark base set (`base #111111`, `contrast #E0DDD5`,
  `secondary`, `muted`, `ghost`), a glass/tint/border set, six category accents
  (`cat-module…cat-changelog`), **and** a parallel `light-*` set (`light-base`,
  `light-surface`, `light-contrast`, … `light-amber`) (`theme.json:10-43`).
- **Typography** — fluid scale (`small…hero`), four font families: Syne, Manrope,
  JetBrains Mono, **Spectral** (`theme.json:45-69`).
- **Spacing** — 8-step custom scale `10…80` (`theme.json:70-82`).
- **Custom props** — `custom.border.{subtle,default,strong}`, `lineHeight`,
  `letterSpacing`, `radius` (`theme.json:84-89`).
- **Styles** — root color/typography/spacing, element styles (`link`, `heading`,
  `h1`–`h4`, `caption`), and ~15 per-block `styles.blocks` entries
  (`theme.json:91-119`). This is correct tier-2/3 placement.

Two evidenced snags carried here (detailed in §4): the `light-*` palette overlaps the
`[data-theme]` CSS override and the `knowledge.json` light palette (one "light" decision
in three places); and the **Spectral `fontFace` `src` files do not exist** in
`assets/fonts/`.

### 3.2 `styles/knowledge.json` (tier 3)

A single named style variation: a **light** palette (`base #F2EFE9`), Spectral as the
body `fontFamily`, Syne for headings, and `custom.border` re-tinted for dark-on-light
(`styles/knowledge.json:1-94`). Correct tier-3 placement for site-wide re-theming. It is
**activated two ways** that must be reconciled (§4, §7):

1. Natively, WordPress would expose any `styles/*.json` as a user-selectable variation in
   the Site Editor.
2. Additionally, `functions.php:44-64` force-merges `styles/{we_style_variation}.json`
   onto `theme.json` via `wp_theme_json_data_theme` when the `we_style_variation`
   theme_mod names it (per `THEME-CONFIG.md`, `knowledge.wickedevolutions.com` sets
   `we_style_variation = 'knowledge'`).

### 3.3 Templates (`templates/`, tier 4) and parts (`parts/`, tier 4)

- **12 templates**: `index`, `home`, `page`, `page-series`, `single-post`,
  `single-post-blog`, `category`, `category-blog`, `archive`, `search`, `404`,
  `front-page-knowledge`. Structure is block markup (correct tier 4). Several are
  near-duplicates (`page.html` ≈ `index.html`; `single-post` vs `single-post-blog`;
  `category` vs `category-blog`) — a tier-4 dedup question deferred to v0.5 (roadmap §5.5).
- **3 parts**: `header.html`, `footer.html`, `sidebar.html` + `theme.json.templateParts`
  (header/footer/sidebar). CHANGELOG `0.3.0` already deleted three byte-identical header
  parts — that consolidation discipline continues in v0.5.
- **The 3-column docs layout** (`single-post.html`, `page.html`): a `wp:columns`
  (`we-page-columns`) with `we-sidebar-col` (260px), `we-content-col`, and `we-toc-rail`
  (240px). Column widths are set as **block attributes**, but the *effective* layout
  (sticky offsets, `flex-basis`/`max-width` overrides, content gutter) is enforced in
  `theme.css` with `!important` (§3.6, §4) — layout partly living at tier 9.
- **Magic constants in markup**: sticky offset `top:112px` (`single-post.html:89`),
  `wideSize:"1200px"` on `main` (`single-post.html:7`, `page.html:7`) **vs**
  `theme.json` `layout.wideSize 1140px` **vs** `footer.html` `wideSize 1140px` — an
  inconsistent wide constant (§4).
- **Version literal**: `parts/header.html:21` hard-codes a `v0.3.1` badge as static block
  markup (currently coherent — §3.9).

### 3.4 Patterns (`patterns/`, tier 5) — incl. TOC and dynamic sidebar

All 12 patterns are `Categories: hidden`, `Inserter: false` (composition/assembly, not
author-facing). Two carry **computed-at-render logic that the ladder assigns to tier 6**:

- **`patterns/toc.php`** — server-side: parses `post_content`, collects H2/H3 headings,
  de-dupes anchors, **injects IDs into `the_content` via an early (priority 5) filter**,
  renders the TOC `<nav>`, and honors `we_disable_toc` post/term meta
  (`patterns/toc.php:1-133`). This is dynamic-block / render-callback behavior (tier 6)
  expressed as a pattern. The minimal plan records the `the_content` regex ID-injection
  as **deliberate** (commit `dae4ba3`) and out of scope — treat as an **intentional
  exception** pending the v0.6 runtime-extraction review.
- **`patterns/nav-sidebar-dynamic.php`** — server-side: resolves a `sidebar_menu`
  meta (post for pages, first category term for posts) and renders `wp_nav_menu`
  (`patterns/nav-sidebar-dynamic.php:1-35`). Also tier-6-shaped logic in a pattern.
- **Static sidebar/nav patterns** — `nav-sidebar-{pages,started,products,concepts,
  agents,policy,reference}.php`, `nav-topbar.php`, `nav-tabstrip.php`: each is a
  `has_nav_menu()`/`wp_nav_menu()` render wrapped in block markup, emitting `we-nav-*`
  classes styled in `theme.css` (correct tier-5 composition; styling at tier 9).
- **`nav-test.php`** — small; appears to be a test/scratch pattern (**needs
  verification**: possible dead pattern).

### 3.5 `functions.php` (tier 7)

Server logic the higher tiers cannot express declaratively. Mostly correct tier-7
placement:

- **Template routing** — `single_template_hierarchy`, `category_template_hierarchy`,
  `frontpage_template_hierarchy` filters gated on `we_*` theme_mods (`functions.php:23-42`).
  Correct: conditional template selection is genuine server logic.
- **Enqueues** (`functions.php:86-118`) — `theme.css` always; `blog.css` conditional on
  `we_enable_blog_css`; `theme-toggle.js` (head) and `toc.js` (footer). Correct tier-7
  registration. Note: the theme enqueues **no** `landing-page.css` — that asset only
  exists as a runtime ability rule (§3.8).
- **Variation force-merge** — `wp_theme_json_data_theme` (`functions.php:44-64`) — see
  §3.2 / §4 / §7 (coexistence with native variation selection is an open question).
- **Light/dark plumbing** — `language_attributes` adds `data-default-theme` from
  `we_default_color_scheme` (`functions.php:66-74`); part of the tier-9 CSS toggle
  mechanism (§4).
- **`body_class`** — emits `we-site-{value}` from `we_body_class` (`functions.php:76-84`)
  for install scoping; consumed by `.we-site-knowledge` CSS (`theme.css:69`).
- **Meta registration** — `sidebar_menu` (page/category), `we_disable_toc`
  (post/category), all `show_in_rest` (`functions.php:124-150`). Correct tier-7.
- **Classic admin UI** — category form fields + post meta boxes + save handlers with
  nonces for sidebar-menu and TOC opt-out (`functions.php:152-297`). Correct tier-7
  (no native block-editor-only equivalent for term meta UI).
- **`register_nav_menus`** — 10 locations incl. 7 `sidebar-*` (`functions.php:299-314`).
  Tier-7 registration; the *set* of locations is site-IA-shaped (§7, roadmap §5.8).

### 3.6 `assets/css/theme.css` (tier 9) — **do not edit this phase**

Header declares "Effects-only CSS. Layout via theme.json + editor settings"
(`theme.css:1-4`). In practice it carries a **mix** of true last-mile effects and
decisions that higher tiers should own:

- **Legitimate tier-9 effects** — transitions, `backdrop-filter`, `::selection`,
  scrollbar styling, hover states, smooth scroll.
- **Layout (should be tier 2/4)** — the 3-column grid, sticky positioning, `flex-basis`/
  `max-width`/padding overrides, all with `!important` (`theme.css:40-69`, `240-258`).
- **Light/dark token *redefinition* (should be tier 2/3)** — `[data-theme="light"]` and
  `[data-theme="dark"]` blocks re-declare `--wp--preset--color--*` and
  `--wp--custom--border--*` at runtime (`theme.css:193-238`). The `dark` block largely
  **duplicates** values `theme.json` already generates.
- **Component visual systems (tier 5 candidates)** — badges, callouts, step lists, lane/
  ability chips, cards, stats bar, ability table, filter chips (`theme.css:82-191`):
  recurring named treatments that are block-style-variation candidates.

Per roadmap §6 and the minimal plan §5, this file is **not** touched this phase. The
minimal plan references a pre-existing dirty FluentCart `--fct-*` mapping block, but on
this branch `assets/css/theme.css` is currently clean; that contrast fix is tracked
separately as its own scoped PR. Treat FluentCart as a placement/boundary question for
the roadmap, not as part of this phase's source diff.

### 3.7 `assets/css/blog.css` (tier 9, conditional)

Category-accent system: `body.category-{slug} { --cat-accent: … }` for six **hard-coded
site-specific category slugs** (`the-experiment`, `the-mirror`, `the-build`, `the-field`,
`the-record`, `the-method`) plus blog component effects (`blog.css:13-67`). Enqueued only
when `we_enable_blog_css` is on. The hard-coded slugs are **site IA encoded in base-theme
CSS** (theme-vs-site boundary, roadmap §5.8 / §7), and the accent hexes overlap palette
tokens (tokenization candidate).

### 3.8 `assets/js/theme-toggle.js` and `assets/js/toc.js` (tier 9)

- **`theme-toggle.js`** — toggles `data-theme` on `<html>`, persists to `localStorage`,
  applies saved/`data-default-theme` before first paint (`theme-toggle.js:1-21`). It is
  the **runtime driver of the entire light/dark model** (§4). Header note cites "Design
  System v1.3 §2.5" (**needs verification**: external/vault reference, not in repo).
- **`toc.js`** — scroll-spy + mobile toggle over the **server-rendered** `.we-toc-list`
  links; explicitly does **no** heading discovery or list generation (`toc.js:1-101`).
  Correct division of labor: PHP (tier 5/6) builds the TOC, JS (tier 9) only enhances it.

### 3.9 Plugin / Abilities / runtime state (tier 8) — from `docs/operations/`

Cited from already-recorded **read-only** inspection (no new calls made here):

- **`theme_mod` capability model** — 10 mods on live `wickedevolutions` incl.
  `we_enable_blog_templates=1`, `we_enable_blog_css=1`, `we_default_color_scheme=dark`,
  `we_body_class=main`, nav locations `topbar=31`/`tabstrip=32`
  (`abilities-inspection-2026-06-02.md`). Written via Abilities, read at tier 7. Correct.
- **Orphan enqueue rule** — `the-mirror-landing-page` → `assets/css/landing-page.css`,
  condition template `page-landing`, `added_by=ability`, `file_exists=false`; no backing
  file, template, or page anywhere in source or site
  (`orphan-landing-enqueue-2026-06-02.md`). Ladder-correct fix = **retire the rule via
  Abilities**, *not* manufacture a stub file. Blocked: no remove/reconcile-enqueue
  ability exists and site is read-only — **logged in `abilities-gap-log.md` (Open)**.
- **Abilities product gaps (Open)** — (a) no enqueue remove/reconcile ability;
  (b) `mcp_adapter_batch_execute` returns `Tool not found` for names that execute
  individually (`abilities-gap-log.md`). Both are MCP-maintainer items, not theme source.
- **Version on live** — active theme reports `0.3.1` (`abilities-inspection-2026-06-02.md`),
  coherent with `style.css`, `functions.php:6` (`@version 0.3.1`), `parts/header.html:21`
  (`v0.3.1`), and `theme.css:2` (`v0.3.1`). Version drift flagged in the minimal plan
  now reads coherent at `0.3.1` in current source (**needs verification** whether
  committed vs working-tree, since this phase ran no `git` mutation).

---

## 4. Decision inventory

Status legend — **correct**: at its owning tier; **review**: placement is arguable or
coordination is unresolved; **misplaced**: a lower tier owns what a higher tier should;
**intentional exception**: lower-tier placement is a recorded, deliberate choice.

| # | Decision | Current owner (tier) | Preferred owner (tier) | Status | Evidence | Next action |
|---|---|---|---|---|---|---|
| 1 | Color palette (dark base, glass/tints, category accents) | `theme.json` (2) | `theme.json` (2) | correct | `theme.json:8-43` | None — keep. |
| 2 | Fluid type scale | `theme.json` (2) | (2) | correct | `theme.json:45-56` | None. |
| 3 | Spacing scale | `theme.json` (2) | (2) | correct | `theme.json:70-82` | None. |
| 4 | Custom props: `lineHeight`, `letterSpacing`, `radius` | `theme.json` (2) | (2) | correct | `theme.json:86-88` | None. |
| 5 | Element/base block styles (link, headings, code, quote, post-* …) | `theme.json` (2) | (2) | correct | `theme.json:91-119` | None — strong native placement. |
| 6 | **Border tokens** (`subtle/default/strong`) | `theme.json` (2) **+** `theme.css` `[data-theme]` (9) **+** `knowledge.json` (3) | `theme.json` + variations (2/3) | review | `theme.json:85`, `theme.css:202,235`, `knowledge.json:55-60` | Decide single source under phase 2 token model; runtime override is a symptom of decision 8. |
| 7 | **Spectral font faces point at missing files** | `theme.json` (2) / `knowledge.json` (3) | (2/3) — but assets absent | misplaced / **needs verification** | `theme.json:61-67`, `knowledge.json:46-52`; `assets/fonts/` holds only Syne, Manrope, JetBrains Mono | Confirm intent: commit `spectral-v13-latin-*.woff2`, or remove/replace the `fontFace`. Impacts knowledge variation body font + `.we-site-knowledge` CSS. |
| 8 | **Light/dark runtime model** | `theme.css` `[data-theme]` overrides (9) + `theme-toggle.js` (9) + `language_attributes` filter (7) | Style variation / global styles (3) | misplaced | `theme.css:193-238`, `theme-toggle.js:1-21`, `functions.php:66-74` | Roadmap phase 4 decision (§5, §7). Headline finding — do not touch CSS until decided. |
| 9 | "Light" encoded three ways | `theme.json` `light-*` (2) + `[data-theme="light"]` (9) + `knowledge.json` (3) | one mechanism at (3) | review | `theme.json:29-36`, `theme.css:194-224`, `knowledge.json:6-39` | Resolve as part of decision 8; pick one light source of truth. |
| 10 | Knowledge style variation | `styles/knowledge.json` (3) | (3) | correct | `styles/knowledge.json:1-94` | Keep; reconcile activation (decision 11) and light model (8/9). |
| 11 | Variation **activation** mechanism | `wp_theme_json_data_theme` force-merge filter (7) + `we_style_variation` mod (8) | Native variation selection (3) +/or pinning at (7) | review | `functions.php:44-64`, `THEME-CONFIG.md` | Decide whether force-merge is needed alongside native Site-Editor selection, or pins installs intentionally (open question §7). |
| 12 | Template/archive/single structure | `templates/*.html` (4) | (4) | correct | `templates/` (12 files) | Keep; dedup is v0.5. |
| 13 | 3-column docs **layout enforcement** | `theme.css` `!important` (9) | block layout attrs / `theme.json` (1/2) + templates (4) | misplaced | `theme.css:40-69`, `single-post.html:10-96` | Phase 3/6: lift sizing/sticky to block settings + tokens; CSS keeps effects only. |
| 14 | Repeated layout constants (`top:112px`, gutter `clamp(24px,5vw,56px)`) | literals in `theme.css`/`toc.js`/templates (4/9) | `theme.json` `custom` (2) | review | `theme.css:47,52,68`, `toc.js:65`, `single-post.html:89`, `header.html:4`, `footer.html:2` | Phase 2: tokenize sticky offset + gutter as custom props. |
| 15 | Inconsistent `wideSize` (1200 vs 1140) | template literals (4) + `theme.json` (2) | `theme.json` layout + presets (2) | review | `single-post.html:7`, `page.html:7` (1200); `theme.json:7`, `footer.html:1` (1140) | Phase 2/3: reconcile to one wide constant. |
| 16 | Component visual systems (badges, callouts, cards, chips, stats, ability table) | `theme.css` global selectors (9) | Block style variations + scoped block CSS (5) | review | `theme.css:82-191` | v0.5 phase 6 — register as block styles; not this band. |
| 17 | Server-rendered TOC + `the_content` ID injection | `patterns/toc.php` (5, doing 6-work) | Dynamic block / render callback (6) | intentional exception | `patterns/toc.php:1-133`; deliberate per commit `dae4ba3` (minimal plan §5) | v0.6 phase 7 — confirm computed-at-render placement; keep as-is for now. |
| 18 | Dynamic sidebar menu routing | `patterns/nav-sidebar-dynamic.php` (5, doing 6-work) | Dynamic block / render callback (6) | review | `nav-sidebar-dynamic.php:1-35`, `functions.php:124-135` | v0.6 phase 7. |
| 19 | Static nav patterns (sidebar/topbar/tabstrip) | `patterns/nav-*.php` (5) + `theme.css` `we-nav-*` (9) | composition (5); styling tokens (2) | correct | `nav-sidebar-pages.php`, `nav-tabstrip.php`, `theme.css:26-80` | Keep composition; nav styling reviewed under phase 6 (v0.5). |
| 20 | `nav-test.php` pattern | `patterns/` (5) | — | review / **needs verification** | `patterns/nav-test.php` | Confirm whether dead; if so remove in v0.5 template/pattern cleanup. |
| 21 | Template routing by capability | `functions.php` filters (7) | (7) | correct | `functions.php:23-42` | None — genuine server logic. |
| 22 | Asset enqueues | `functions.php` (7) | (7) | correct | `functions.php:86-118` | None. |
| 23 | Post/term meta registration (`sidebar_menu`, `we_disable_toc`) | `functions.php` (7) | (7) | correct | `functions.php:124-150` | None. |
| 24 | Classic admin UI (meta boxes, term fields) | `functions.php` (7) | (7) | correct | `functions.php:152-297` | None — no native equivalent for term-meta UI. |
| 25 | Nav menu **location set** (10, incl. 7 `sidebar-*`) | `functions.php` `register_nav_menus` (7) | (7) registration; **set** is site-IA | review | `functions.php:299-314` | v0.6 phase 8 — decide which locations are base-theme vs site IA. |
| 26 | Category→accent color map (6 hard-coded slugs) | `blog.css` (9) | tokens (2) + theme/site boundary (8/—) | misplaced | `blog.css:13-19` | Phase 8 (v0.6): site IA out of base CSS; tokenize accents. |
| 27 | TOC scroll-spy / mobile toggle | `toc.js` (9) | (9) | correct | `toc.js:1-101` | None — correct last-mile enhancement of server output. |
| 28 | Install capability model (`theme_mod`s) | runtime/Abilities (8) read by `functions.php` (7) | (8)/(7) | correct | `THEME-CONFIG.md`, `functions.php`, `abilities-inspection-2026-06-02.md` | None. |
| 29 | Orphan `the-mirror-landing-page` enqueue rule | runtime ability state (8) | (8) — retire rule | misplaced (defect) | `orphan-landing-enqueue-2026-06-02.md`, `abilities-gap-log.md` | Retire via Abilities once read-only lifted + remove-ability exists; **not** this phase. Gap is Open. |
| 30 | Version coherence | static literals across 4 files (2/4/7/9) | single source `style.css` | correct (currently `0.3.1`) | `style.css:7`, `functions.php:6`, `header.html:21`, `theme.css:2` | None now; **needs verification** committed vs working-tree. |

---

## 5. Candidate phase backlog — v0.4 native foundation only

v0.4 closes **roadmap phases 2–4** (token model, CSS-into-`theme.json`, light/dark
decision). It establishes that the design system lives in `theme.json` + global styles.
The backlog below is **scoped to v0.4**; later bands are notes only.

| v0.4 work item | Roadmap phase | Inventory rows | Nature |
|---|---|---|---|
| **Role/intent token model.** Audit `theme.json` presets for semantic role naming (surface / text / accent / border) vs raw values; decide the token vocabulary *before* migrating any CSS into it. | 2 | 1, 6, 14, 15 | Decision (vocabulary), no CSS deletion. |
| **Tokenize repeated layout constants.** Sticky offset (`112px`), content gutter (`clamp(24px,5vw,56px)`), and a single reconciled `wideSize` as `theme.json` custom props. | 2 | 14, 15 | Promotion to tier 2. |
| **Resolve Spectral font assets.** Decide commit-the-files vs remove-the-`fontFace`; a prerequisite to trusting the knowledge variation's body type. | 2 (token model hygiene) | 7 | Asset/decision; **needs verification** of intent. |
| **Global CSS → `theme.json` candidates.** Identify values hard-coded in `theme.css` that a preset/setting should own (border tokens duplicated at runtime, layout sizing). Migrate **by promotion**, not hand-deletion, and only after rows above are decided. | 3 | 6, 13, 14 | Promotion to tier 2/3. |
| **Light/dark / style-variation decision.** Decide whether dark-first + light is a style variation / global-styles mechanism (tier 3) rather than the `[data-theme]` CSS toggle + JS. Settle the "one light source of truth" question. | 4 | 8, 9, 10, 11 | **Headline decision**; gates any CSS light/dark cleanup. |

**Ordering constraint (roadmap §5):** the token vocabulary (phase 2) is decided *before*
any CSS migration (phase 3); the light/dark model (phase 4) is decided *before* the
`[data-theme]` blocks are touched. **No CSS cleanup precedes its tier's decision.**

### Later bands (v0.5 / v0.6) — noted, **not** in v0.4

- **v0.5 (phases 5–6):** template/part dedup (rows 12, 20), component → block-style
  variations (row 16), nav styling (row 19).
- **v0.6 (phases 7–10):** runtime extraction of TOC + dynamic sidebar to dynamic
  blocks/render callbacks (rows 17, 18); theme-vs-site/product boundary incl. nav
  locations and category-accent slugs (rows 25, 26); Abilities verification harness +
  orphan-enqueue retirement (row 29); human editor QA.

A later-band item is pulled into v0.4 **only if it blocks** a v0.4 decision. Current
read: none block phases 2–4 except the **Spectral asset** question (row 7), which the
token-model phase must resolve because the knowledge variation depends on it.

---

## 6. Guardrails

- **Visual parity.** Every v0.4 change is diffed for visual result against `v0.3.1`; any
  deviation is deliberate, called out, and signed off — never an accidental byproduct of
  moving a decision up a tier (roadmap §7).
- **Editor-native inspectability.** A decision is only "moved" when it can be **inspected
  and edited in the Site Editor / Global Styles**, not merely relocated in source files
  (roadmap §7). Token and light/dark work must land where the editor can see it.
- **No CSS cleanup before the authority decision.** No selector in `theme.css`/`blog.css`
  is renamed, deleted, or reorganized until its owning tier's decision (phases 2–4) is
  made. CSS shrinks by **promotion**, not premature tidying (roadmap §6).
- **`assets/css/theme.css` is off-limits this phase.** Do not stage, revert, or extend it;
  FluentCart contrast work is a separate scoped branch/PR and this spec branch should not
  mix that visual fix into the authority inventory.
- **Abilities verification only after explicit authorization.** Any live/editor-state
  check or mutation (e.g. orphan-rule retirement) is a separately-authorized read-only-
  then-decide Abilities pass (roadmap §5.9; `AGENTS.md §2,§4`). The site is in read-only/
  inquiry mode; this phase makes **no** WordPress calls.
- **Promotion, not expansion.** No new colors, fonts, spacing, or surfaces — this is a
  placement refactor (roadmap §6). (Resolving the Spectral assets is *coherence*, not
  expansion.)
- **Public repo.** No secrets/local config enter any artifact (`AGENTS.md §3`).

---

## 7. Open questions — require user / design decision before code

These cannot be resolved from repo evidence alone and **gate** the relevant v0.4 phases:

1. **Light/dark model (gates phase 4).** Should light/dark be a true tier-3 style
   variation / global-styles mechanism (editor-selectable, re-skinnable by role), or does
   the product *require* the instant client-side `[data-theme]` toggle with `localStorage`
   persistence and pre-paint default? If both, which is the **source of truth** and which
   is generated from it? Today it is encoded three ways (rows 8, 9).
2. **Role token vocabulary (gates phase 2).** What are the canonical role names —
   e.g. `surface` / `surface-raised` / `text` / `text-muted` / `accent` / `border-*`?
   Do category accents (`cat-*`) and FluentCart bridge vars fold into the role model or
   stay as a separate semantic set? This vocabulary must be agreed before any CSS migrates.
3. **Variation activation (gates phases 3–4).** Is the `wp_theme_json_data_theme`
   force-merge (row 11) an intentional install-pinning mechanism that must coexist with
   native Site-Editor variation selection, or should it be retired in favor of native
   selection? Affects how "knowledge" and any future variation are chosen.
4. **Spectral fonts (gates phase 2).** Are the `spectral-v13-latin-*.woff2` files meant to
   ship (commit them) or was Spectral deprecated (remove the `fontFace`)? The knowledge
   variation's body font and `.we-site-knowledge` typography depend on the answer (row 7).
5. **FluentCart placement (boundary question, surfaces in v0.4).** Does FluentCart theming
   belong in the **base theme** (a maintained tier-2/3 token bridge) or as a **scoped
   integration** (tier-9 last-mile, or a separate plugin/child concern)? The scoped
   FluentCart contrast branch/PR forces this question even though §6 forbids editing
   `theme.css` here. *(Decision only; no edit this phase.)*
6. **Theme identity (frames the whole conversion).** Does this remain the bespoke *Wicked
   Evolutions* theme, or is the v0.4+ goal a **reusable, role-tokenized artifact** that WE
   is one consumer of? This changes how aggressively site IA (category slugs in `blog.css`
   row 26, the `sidebar-*` location set row 25, `we_body_class` scoping) is pushed out of
   the base theme in v0.6 — and whether v0.4 token names should be product-neutral now.

---

## 8. Relationship to the roadmap and the minimal plan

- **[`v0.4-native-core-refactor-roadmap.md`](../plans/v0.4-native-core-refactor-roadmap.md)
  — parent.** This document **is** roadmap **phase 1** (§5.1: "Write down, per surface,
  which tier currently owns each decision and which tier *should*… the audit map that
  every later phase consumes. No code changes."). §4 here instantiates the roadmap's §4
  decision-ownership table against concrete repo evidence; §5 here sequences only the
  roadmap's v0.4 band (phases 2–4, roadmap §9). The roadmap remains the authority for
  phase order, acceptance criteria (§7), and non-goals (§6); this spec does not restate or
  override them.
- **[`v0.4-native-core-minimal-plan.md`](../plans/v0.4-native-core-minimal-plan.md)
  — sibling, narrower.** The minimal plan is the **single first safe slice** (orphan
  landing-enqueue reconciliation + version coherence) and is, at most, *seed work* toward
  phase 1 and phase 9. This spec is the **full phase-1 inventory** the minimal plan
  deferred: it carries the orphan-enqueue and version findings forward as inventory rows
  (29, 30) but does not act on them, and it extends the audit to every surface the minimal
  plan left open. Nothing here settles the broader token/variation/template/runtime/
  boundary decisions — those are **opened** (not closed) as §5 backlog and §7 questions.
- **`AGENTS.md` — contract above both.** The §2 authority ladder is copied/mapped in §2
  here; all working discipline, Abilities-first, public-repo, and no-deploy rules apply
  unchanged.

**This phase's exit:** the inventory (§4), v0.4 backlog (§5), and open questions (§7)
exist and are reviewable against the ladder. No source changed. Phase 2 (role token
model) begins only after the §7 questions that gate it are answered.
