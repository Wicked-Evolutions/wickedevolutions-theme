# Phase 3 — CSS-necessity map (`assets/css/theme.css`)

> **Phase 3 of the v0.4 native foundation** (roadmap §5.3 — CSS into the authority ladder).
> **Issue:** #94 · **Branch:** `v0.4-css-necessity` · **Date:** 2026-06-02.
> **Scope (orchestrator-approved, banded 1A/2A):** classify every `theme.css` rule by
> responsibility; migrate the in-band layout decisions up the ladder; tokenize the repeated
> sticky constant; normalize token references; confirm FluentCart is out; deliver this map.
> Component visual systems, light/dark, and content-width are **explicitly deferred** to
> their owning phases (named per row below) — not removed this phase.
>
> Read against [`AGENTS.md`](../../AGENTS.md) §1 (authority ladder) and the
> [`native-authority-spec.md`](native-authority-spec.md) decision inventory (rows 6, 13, 14, 16).

---

## 1. Restated acceptance (per orchestrator, 2026-06-02)

`theme.css` cannot be **100% effects-only** this phase while the deferred bands stand. The
gate for #94 is therefore:

1. the **in-scope band complete** (layout `!important` reduced by fixing the level; sticky
   constant tokenized; references normalized; FluentCart confirmed out), and
2. **parity-verified** (no visual change), and
3. `theme.css` reduced to **effects + the explicitly-deferred bands**, with
4. this **classified map** naming each deferred rule's native home + owning phase.

## 2. Responsibility buckets

| Bucket | Meaning | Disposition this phase |
|---|---|---|
| **effect** | transitions, `backdrop-filter`, `::selection`, scrollbars, hover/transform, smooth-scroll | **keep** in `theme.css` (its correct tier-9 home) |
| **layout-override** | the 3-column docs grid: sizing, sticky, padding | **in-band** — `!important` removed by fixing the level; constant tokenized; remainder is clean tier-9 layout (block-attribute lift is a verify-gated follow-up, §6) |
| **token-value** | hard-coded constant a preset/custom-prop should own | **in-band** — the `112px` sticky offset promoted to `theme.json` |
| **component visual system** | badges, callouts, chips, cards, stats, ability-table, nav skins | **deferred → v0.5 phase 6** (register as block styles; audit row 16) |
| **light/dark token redefinition** | `[data-theme]` blocks re-declaring `--wp--preset--*` | **deferred → #97 / Phase 4** (rows 8/9; gated) |
| **content-width** | `.we-content-col` width / `wideSize` | **deferred → #95 / Phase 4** |
| **plugin-integration** | FluentCart / WooCommerce CSS | **n/a — none in the base stylesheet** (§5) |

## 3. What changed this phase

| # | Change | Lines (new file) | Why parity-safe |
|---|---|---|---|
| 1 | **Tokenize sticky offset.** Added `settings.custom.layout.stickyTop = "112px"` → `--wp--custom--layout--sticky-top`; replaced the three CSS literals + the `calc(100vh - 112px)` / `calc(112px + 32px)` expressions with the token. | `theme.json:97`; `theme.css:49,54,70` | Same computed value — pure `var()` substitution. |
| 2 | **Remove dead code.** Deleted the two `main.we-has-sticky-columns …` rules — the class is applied by **no** template/part (only referenced in CSS). | (removed) | Selector never matched → no render effect. |
| 3 | **Drop superfluous `!important` (fix the level, don't escalate).** `.we-page-columns` margin/padding/gap; `.we-sidebar-col` and `.we-toc-rail` position/top/align-self/padding/margin; the redundant `:where(.wp-block-columns).we-page-columns{margin-bottom}`; the responsive `.we-toc-rail`/`.we-sidebar-col { display:none }`. | `theme.css:46,49,54,243,251` | These only beat 0-specificity `:where()` core defaults or same-spec-earlier rules; the column `<div>`s carry **only** `flex-basis` inline and these columns declare no spacing/align attrs, so nothing competes. `!important` was superfluous. |
| 4 | **Header comment** rewritten to state the effects-only target + the deferred bands + the token. | `theme.css:1-9` | comment only |

**Result:** `!important` 30 → 24 lines (15 occurrences removed); file integrity preserved (braces balanced). The remaining 24 are all justified below.

## 4. Per-section classification (every rule region)

| `theme.css` section | Bucket | Native home | Status / owning phase |
|---|---|---|---|
| GLOBAL: smooth-scroll, antialias, `body` transition, `::selection`, `a` transition, `.wp-block-code` overflow | **effect** | tier 9 | **keep** |
| GLOBAL: post-content `strong`/`em` color, inline `code` chip | component (typography) | tier 2/5 | v0.5 (small; or `styles.blocks`) |
| HEADER: `backdrop-filter`, `z-index`, all `:hover`/transitions, theme-toggle button | **effect** | tier 9 | **keep** |
| HEADER: site-title dot, `we-version-badge`, `we-tab*`, `we-nav-topbar/tabstrip*` skins | component (nav) | block styles | **v0.5 phase 6** |
| HEADER: `.we-icon-moon/-sun` toggle visibility | light/dark | variation/global-styles | **#97 / Phase 4** |
| 3-COL LAYOUT: `.we-page-columns`, `.we-sidebar-col`, `.we-toc-rail` sizing/sticky/padding | **layout-override** | block layout attrs (tier 1) + `theme.json` (tier 2) | **this phase** — `!important` removed, `112px` tokenized; per-column block-attribute lift = verify-gated follow-up (§6) |
| 3-COL LAYOUT: `.we-content-col` `flex-basis/max-width:800px` + responsive | **content-width** | `theme.json` layout / block width | **#95 / Phase 4** (left byte-identical) |
| 3-COL LAYOUT: `.we-page-columns … h1` clamp `!important` | layout/typography (contextual) | block style / per-block setting | **kept** (defensive contextual override; v0.5 candidate) |
| 3-COL LAYOUT: `.we-site-knowledge … p/li/blockquote { spectral }` | token/typography (install scoping) | knowledge **variation** (tier 3) vs body-class | **review** — variation-vs-body-class boundary (touches the Phase-2/activation question); left as-is |
| SIDEBAR, CATEGORY BADGES, BLOG, CALLOUTS, RULED/EYEBROW, STEP LIST, LANE/ABILITY CHIPS, SIDEBAR SEARCH/DOTS, FILTER CHIPS, CARD HOVER, QUICK-LINK/RELATED, STATS BAR, ABILITY TABLE | **component visual system** | block style variations + scoped block CSS | **v0.5 phase 6** (audit row 16) — appearance kept; `:hover`/transition halves are legit effects |
| DOCS category-row `:hover` | **effect** | tier 9 | **keep** |
| LIGHT MODE / DARK MODE (`[data-theme]` blocks) | **light/dark token redefinition** | style variation / global styles (tier 3) | **#97 / Phase 4** (headline; gated — untouched) |
| RESPONSIVE: `.we-toc-rail`/`.we-sidebar-col { display:none }` | layout-override | media + block | **this phase** — `!important` dropped |
| RESPONSIVE: `.we-content-col` padding/width | content-width | `theme.json` / block | **#95 / Phase 4** (kept) |
| RESPONSIVE: `.blog-post-cards` grid | component (blog) | block layout (query grid) | **v0.5** (kept; beats the query block's inline grid) |

### The 24 remaining `!important`, each justified

- **Nav/sidebar/component hovers + skins** (`we-topbar/we-tab/we-tabstrip` hover; `we-sidebar p/title/label`; `we-sidebar` active; `blog-post-card` hover; archive-row hover; `we-callout*` padding; `we-cat-dot a`): **component band (v0.5)** — most beat core-block inline styles; removed when these become block styles.
- **`.we-page-columns … h1` clamp:** defensive contextual typography (kept).
- **`.we-content-col` (×2 incl. responsive):** **content-width (#95)** — left byte-identical.
- **`.admin-bar … { top }`:** genuinely required — must beat the toc-rail's block-support-**injected** inline `top` (position support is active; confirmed by `is-position-sticky` in the live render).
- **`.blog-post-cards` grid (×2):** beats the query block's inline grid (component/blog band).
- **`[data-theme] … .we-header` / sidebar active (×3):** **light/dark (#97)** — untouched.

## 5. FluentCart

`grep -rin 'fluent|fct|woocommerce|checkout' assets/css/` → **none**. The base stylesheet
contains **no** plugin-integration CSS, so "scope FluentCart out" needs no source change. The
FluentCart contrast work referenced by earlier audits lived on a separate/uncommitted branch
(per `native-authority-spec.md` §3.6) and never landed in base `theme.css`. If it returns, its
native home is a **conditionally-enqueued / scoped integration stylesheet**, not base
`theme.css`.

## 6. Reference-form normalization (folded in)

The target was to converge token references toward native slug-binding where a block should
bind a token, keeping raw `var()` only where genuinely CSS-level. Findings:

- **Templates/parts are already convergent.** Color/font bindings use slug attributes
  (`textColor`/`backgroundColor`/`fontFamily`, normalized to roles in Phase 2); other style
  props use the native `var:preset|…` / `var:custom|…` shorthand. Repo-wide grep finds
  **zero** raw `var(--wp--…)` and **zero** hardcoded hex/rgba in template **attribute** JSON.
  (The raw `var()` that exist are CSS-level in `theme.css` — correct — and auto-serialized
  inline HTML — not hand-authored.) → **no edits needed; documented as convergent.**
- **`theme.css` keeps raw `var()`** — correct for the CSS tier. A separate, parity-neutral
  cleanup could align CSS name-slug refs (`--…--syne`, `--…--yellow`) to the Phase-2 role
  slugs (`--…--heading`, `--…--accent-1`); deferred as cosmetic to avoid a large diff this
  phase.

## 7. The deferred block-attribute lift (verify-gated)

Row 13's ideal end state moves the 3-column **sizing/sticky/padding** out of `theme.css`
into per-column **block attributes** (tier 1) + `theme.json` (tier 2). This phase promoted
the constant (tier 2) and removed the specificity escalation, leaving clean tier-9 layout
rules. The remaining lift — writing the block-support serialization into the column blocks so
the CSS rules can be deleted — is **not done blind**: it requires editor/render validation to
serialize the supports byte-exactly (else block-validation breaks), which cannot be confirmed
under the deploy-gate (live is v0.3.1; changes are undeployed). It is the concrete next action
once a deploy-preview or Playground pass is authorized.

## 8. Parity verification

- **JSON + CSS integrity:** `theme.json` parses; `theme.css` braces balanced (188/188); no
  stray `112px` literal remains in CSS.
- **By construction:** every change is value-preserving — token substitution (same value) or
  removal of `!important` that only beat 0-specificity `:where()` defaults / same-spec-earlier
  rules, against columns whose only inline style is `flex-basis` (verified by reading the saved
  `<div>`s). Dead-code removal targeted a class no template applies.
- **Abilities (read-only):** `themes/design-snapshot` baseline captured (deployed v0.3.1);
  `content/render-page` baseline captured. Position support confirmed active in the live render
  (`is-position-sticky`), which grounds the `!important` analysis above.
- **Deploy-gated:** the *effective* before/after `render-page` diff on the changed CSS requires
  a deploy (no live mutation this phase) — same gate as Phase 2. Until then parity rests on the
  by-construction argument; the post-deploy `design-snapshot` + `render-page` compare is the
  follow-up gate.
