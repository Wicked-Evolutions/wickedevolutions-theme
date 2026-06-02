# Phase 3 follow-up — sticky-top block-attribute lift: finding

> **Branch:** `v0.4-sticky-top-lift` · **Date:** 2026-06-02 · Follows the merged Phase 3 (#94).
> **Outcome:** **NO canonicalization** — proven in Playground. The two saved
> `position.top:"112px"` block-attribute literals are **inert** (their block doesn't support
> position), so they are **left as-is** and the **CSS `--wp--custom--layout--sticky-top`
> token is the canonical source**. This is the task's documented "NO" branch — an acceptable
> outcome, not a failure. **No source/CSS/template bytes changed; docs only.**

---

## 1. The question

Phase 3 tokenized the CSS sticky offset but left two saved block-attribute literals
(`templates/single-post.html:89`, `templates/category.html:74`):

```html
<!-- wp:column {"width":"240px","style":{"position":{"type":"sticky","top":"112px"}}, …} -->
```

Can `position.top` be canonicalized to the token — e.g. `"top":"var:custom|layout|sticky-top"`
(→ `var(--wp--custom--layout--sticky-top)`) — and still validate + render identically?

## 2. Handbook finding (mcp-obsidian "Wordpress Developers Handbooks")

`Theme Handbook/.../Settings/Position.md` documents **only** `settings.position.sticky`
(the UI toggle) and states the control appears "for blocks that support the position feature,
**such as Group**." Column is **not** listed. The `var:custom|…|…` shorthand is documented
**only** for `theme.json` **styles** (style-engine context — `Settings/Custom.md`,
`Styles/Using Presets.md`), never for block-level position. Two signals: (a) Column may not
support position at all; (b) there is no documented token form for `position.top`.

## 3. Playground proof (decisive)

`@wp-playground/cli` (WP 7.0, PHP 8.3), theme auto-mounted + active. Rendered `core/column`
and `core/group` (the known position-supporting control) across all three `top` forms via
`do_blocks()`:

```
column supports position?  false
group  supports position?  true
sticky-top token resolves in stylesheet?  YES (112px)

COLUMN literal    -> <div class="wp-block-column t is-layout-flow …" style="flex-basis:240px">
COLUMN rawvar     -> <div class="wp-block-column t is-layout-flow …" style="flex-basis:240px">
COLUMN shorthand  -> <div class="wp-block-column t is-layout-flow …" style="flex-basis:240px">
GROUP  literal    -> <div class="wp-block-group … wp-container-1 is-position-sticky">
GROUP  rawvar     -> <div class="wp-block-group … wp-container-2 is-position-sticky">
GROUP  shorthand  -> <div class="wp-block-group … wp-container-3 is-position-sticky">
```

**`core/column` does not support position sticky.** Its `position` attribute is **wholly
inert** — literal, raw `var()`, and `var:custom|` shorthand all produce the **identical**
position-less `<div>` (no `is-position-sticky`, no `position`, no `top`). `core/group` (the
control) *does* support it and emits a scoped `wp-container-N` rule, confirming the mechanism
works and the test is valid.

**Front-end render (real `single-post.html`, `http://…/?p=1`, HTTP 200):**

- `.we-sidebar-col` / `.we-toc-rail` columns render `style="flex-basis:…"` **only** — no
  `is-position-sticky`, no `position`/`top`. (The page's 3 `is-position-sticky` are the
  **header Group**, which legitimately supports position.)
- Served `theme.css` is the merged tokenized Phase-3 file
  (`.we-toc-rail { position: sticky; top: var(--wp--custom--layout--sticky-top); … }`).
- `--wp--custom--layout--sticky-top: 112px` resolves in the inline global styles.
- The page is valid (HTTP 200, layout assembles) → the inert `position` attr causes no
  block-validation error.

## 4. Decision (task "NO" branch)

The lift is **not possible via the token** — but for a more fundamental reason than the task
anticipated ("`position.top` takes a literal length"): **`core/column` doesn't support
position at all**, so *no* value (token or literal) on `position.top` is honored. The sticky
behavior is, and always was, **CSS-driven** — already tokenized in Phase 3.

Per the task: **leave the hardcoded values, document the constraint, CSS token is canonical.**
Done — no source change. The two literals are inert metadata in the saved markup; they are not
a serialization source.

## 5. Verification against the task's checklist

| Required check | Result |
|---|---|
| sticky sidebar + TOC behavior | CSS-driven; `.we-sidebar-col`/`.we-toc-rail` ship `position:sticky; top:var(--…--sticky-top)`; token resolves to `112px` (Playground). |
| admin-bar offset (`+32px`) | `.admin-bar .we-{sidebar-col,toc-rail}{ top: calc(var(--…--sticky-top) + 32px) }` → `144px`; token resolves. |
| no block-validation breakage | Column ignores the `position` attr; saved `<div>` (flex-basis only) == serialized output → valid (front end HTTP 200; isolated `do_blocks` clean). |
| no visual regression | **No change made** — literals left untouched, CSS unchanged → regression impossible. |

## 6. Corrections to the merged Phase-3 map

This finding **falsifies two claims** in `phase-3-css-necessity-map.md` (now corrected there,
pointing here):

1. The map said the `.admin-bar` `!important` is "required to beat the toc-rail's
   block-support-**injected** inline `top`." **There is no injection** — column doesn't
   support position. The `.admin-bar .we-toc-rail` selector (0,2,0) beats the base
   `.we-toc-rail` (0,1,0) on specificity alone, so that `!important` is **superfluous (but
   harmless)**, not "genuinely required."
2. The map listed the two literals as "deferred to the block-attribute lift." That lift is
   now **resolved as impossible**; they are inert, and the CSS token is canonical.

## 7. Recommended optional cleanup (NOT done here — needs orchestrator OK)

Two parity-neutral, Playground-verifiable tidy-ups, deliberately left out of this slice (the
task said *leave* the literals):

- **Remove the inert `position` attr** from the two toc columns (`single-post.html:89`,
  `category.html:74`). Proven safe: the column already serializes to `flex-basis`-only, so
  removing the ignored attr changes nothing rendered and removes misleading dead metadata.
- **Drop the now-superfluous `!important`** on `.admin-bar .we-{sidebar-col,toc-rail}` (it
  wins on specificity without it).

Both are tiny and I can verify them in the same Playground harness if approved. Left as a
follow-up so this slice stays a faithful "document + leave" per the task.
