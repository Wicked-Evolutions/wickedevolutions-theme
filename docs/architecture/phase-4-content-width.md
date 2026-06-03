# Phase 4 (slice 1) — content-width 832 (issue #95)

> **Branch:** `v0.4-content-width` · **Date:** 2026-06-02 · Light/dark (#97) and v0.5 out.
> **Locked target:** docs 3-column middle column = **832px (52rem)** on desktop, **single-sourced
> in `theme.json` `settings.layout.contentSize`**, user-editable via Site Editor → Styles →
> Layout → **Content width**; the `.we-content-col { 800px !important }` CSS authority removed.
> **Outcome:** the docs middle column is a token-driven width (832) and the centred band **tracks
> `contentSize`** so the Site Editor Content-width control governs it with **no TOC-edge gap**
> (Playground-measured with realistic content; editing contentSize 832→700 moves column + band,
> proportions intact). No deploy.
>
> **Correction (2026-06-03):** the first version of this slice (PR #103, deploy-tested) made the
> content-col **fill** (`flex-grow:1`) and relied on `post-content` constrained to cap the readable
> text. That **regressed the live 3-column layout** — `contentSize` sizes only the *constrained
> content inside* a flex column, not the column itself, so the flex columns redistributed (sidebar
> too wide, middle under-sized, TOC right-edge gap). The isolation harness (empty Hello-World post)
> masked it; re-verified with a rich post + sidebar menu + computed-width gap measurement. §3–§5
> document the **corrected** fix.

---

## 1. Target reconciliation (REFERENCE was stale)

The `REFERENCE — Claude Code Docs Layout Specification` measured **598–721px** — **superseded**.
Issue #95 **locks 832px (52rem)** as the docs content width, matching current code.claude.com/docs
+ hermes-agent docs; the prototype + live knowledge site are corrected *toward* 832 (the
prototype is wrong on this dimension). 832 is an **interim** value — cheap to change now that it
is single-sourced.

## 2. Native mechanism (Handbook)

`Settings/Layout.md`: `settings.layout.contentSize` is the default content width; a block with a
**constrained** layout and **"Inner blocks use content width"** (no explicit `contentSize`)
**inherits** it. WordPress emits it as the `--wp--style--global--content-size` CSS var, and the
Site Editor **Content width** control edits `settings.layout.contentSize`. So the native path to
"editable content width governing the docs column" is: **`post-content` constrained, inheriting
`contentSize`** — exactly what the issue specifies ("the middle column must inherit this via a
constrained layout — not a hardcoded flex-basis/max-width").

## 3. What the investigation found (Playground, playwright computed widths)

**Baseline (current `main`, contentSize 800, `.we-content-col {800!important}`):** the middle
column **actually rendered 740, readable text 644** — *not* 800. The `800` was a never-reached
cap: the docs band (`.we-page-columns`) is capped at the template `main` `wideSize` (1200), so the
fixed-basis columns **flex-shrank** to fit (sidebar 260→210, toc 240→249, content 800→740).

**Regressed approach (PR #103) — content-col *fills* (`flex-grow:1`), `post-content` constrained
caps the readable text.** With an empty post this *measured* fine, but on live (real content) it
broke: a filling flex column with the readable text centred inside leaves whitespace between the
text and the TOC (the "TOC right-edge gap"), and the redistribution made the sidebar too wide and
the middle under-sized. Root cause: **`contentSize` does not size a flex column** — it caps the
*constrained content inside* it. So the column needs its own width authority.

**Corrected approach — the column keeps a width authority pointed at the token, and the band
tracks `contentSize`.** Two parts:
1. `.we-content-col` regains a width: `flex-basis/max-width: var(--wp--style--global--content-size);
   flex-grow: 0` (WP generates that var from `theme.json` `layout.contentSize`). The column is now
   832, token-driven, not a hardcoded `800px`.
2. The centred docs band is sized to **exactly** the column sum so there is no slack (no gap) and it
   still tracks the token: `.we-page-columns--3col { width: calc(653px + var(--…--content-size)) }`
   (overhead 653 = sidebar 261 incl. 1px border + toc 296 incl. padding + content padding 96);
   `--2col` (front page, no toc) = `calc(357px + var)`. `max-width:100%` + default `flex-shrink`
   keep it graceful on narrow viewports. Editing `contentSize` resizes the column **and** the band
   together, so proportions never break and no gap opens.

(Earlier rejected idea — `width: fit-content` on the band — failed: flex intrinsic sizing summed the
columns' *content* widths, not their flex-basis, shrinking everything wrongly. The explicit `calc`
is deterministic.)

## 4. The changes (4 files)

| File | Change |
|---|---|
| `theme.json` | `settings.layout.contentSize` **800 → 832** (the single source; `wideSize` stays 1140 site-default). |
| `assets/css/theme.css` | `.we-content-col` width pointed at the token: `flex-basis/max-width: var(--wp--style--global--content-size); flex-grow:0` (was `800px !important`). `.we-page-columns` becomes a centred container (`max-width:100%; margin-inline:auto`) with `--3col`/`--2col` modifiers carrying the contentSize-tracking `width: calc(<overhead> + var)`. |
| `templates/single-post.html` | `post-content` `contentSize:"800px"` → **inherit**; columns block gains the `we-page-columns--3col` modifier; docs band `main` `wideSize` reverted **1500 → 1200** (the band is governed by the calc, not `wideSize`). |
| `templates/front-page-knowledge.html` | `post-content` `contentSize:"800px"` → **inherit**; columns block gains the `we-page-columns--2col` modifier (2-col band, no toc). |

`wideSize` decision (#95): the docs band is no longer governed by `main` `wideSize` (it's the
`calc`-tracked band); `wideSize` is left at the original `1200` per template and `1140` site-default.

## 5. Playground evidence (computed widths, playwright/chromium, **rich content** — post with H2/H3 + sidebar menu)

**single-post (docs 3-col), contentSize 832:**

| viewport | readable | sidebar | toc | TOC-edge gap | note |
|---|---|---|---|---|---|
| 1920 | **832** | 261 | 296 | **0** | full 3-col, band 1485 = calc(653+832) |
| 1600 | **832** | 261 | 296 | **0** | band 1485 |
| 1440 | 734 | 230 | 268 | **0** | graceful (band viewport-limited, proportional) |
| 1280 | (toc hides) | 122* | — | — | `@≤1280` toc hides, content fills — *sidebar 122 is **pre-existing**, see §7 |

Wide-desktop: readable **= 832**, sidebar/toc at their **intended** widths (261/296, un-shrunk), and
the TOC-edge **gap = 0** (the #103 regression). Below the band width it shrinks proportionally with
no gap.

**Editability — the control governs it (decisive):** setting `contentSize` **700px** moved the
readable column to **exactly 700** *and* the band to **1353** (= calc(653+700)), with **sidebar 261 /
toc 296 unchanged and gap 0**. So the Site-Editor Content-width control resizes the column and the
band together — proportions hold, no gap.

**Baseline comparison (origin/main, same rich content):** `@1920` readable 644 / sidebar 210 / toc
249 (band capped at 1200, all flex-shrunk); `@1280` sidebar **122** — i.e. **identical** to this
branch at 1280, confirming that squish is pre-existing (§7).

## 6. Regression scope (intended single-source consequence)

`contentSize` is the **site default**, so changing 800→832 widens every template whose
`post-content` *inherits* it: the docs (single-post, front-page-knowledge → 832 via the band) **and
`index.html` + `page.html`** (their `post-content {constrained}` → 832, was 800). **Blog templates
keep their explicit widths** (`single-post-blog` 680, `page-series` 520/680, `category-blog` 680) —
unaffected. This is the deliberate single-source tradeoff: one editable default (832) for all
non-blog content. If index/page must stay 800, give them an explicit `contentSize` (still native) —
flagged for the design pass.

## 7. Honest caveats (for the design pass)

- **832 needs a wide viewport.** The overhead (sidebar 261 + toc 296 + content padding 96 = 653)
  means the 3-col band must be ≥ ~1485px for the column to reach 832 — i.e. **~1600px viewport**.
  Below that the band is viewport-limited and the whole band shrinks **proportionally with no gap**
  (e.g. @1440 readable 734). If 832 is wanted at 1440, the *design pass* must narrow the
  sidebars/padding — out of scope. (832 is interim per the issue.)
- **The `calc` overhead (653 / 357) is a hardcoded constant.** It encodes the fixed chrome
  (sidebar/toc/padding). If those dimensions change, update the `calc`. This is the price of a
  centred, gap-free, contentSize-tracking band that core's constrained layout can't express for a
  flex columns row (`fit-content` was tried and is unreliable — §3).
- **Pre-existing `@≤1280` sidebar squish.** At the `@≤1280` breakpoint the toc hides and content
  fills via the existing `flex-basis:auto !important` responsive rule; with **long** content the
  content column claims its intrinsic width and the sidebar squishes (~122px). This is **measured
  identical on `origin/main`** (§5 baseline) — it predates this slice and is **out of scope**.
  A future pass could set the responsive content-col `min-width:0` / `flex-basis:0` to fix it.

## 8. Verification summary (task checklist)

- **3-column layout verified in full** (rich post + sidebar menu, computed widths) — the #103
  regression is gone: wide-desktop readable **832**, sidebar **261** / toc **296** at intended
  widths, **TOC-edge gap 0**; graceful proportional shrink below; responsive breakpoints intact.
- **Editing contentSize moves the column AND keeps proportions** ✓ (contentSize 700 → readable 700,
  band 1353, sidebar/toc unchanged, gap 0) — the Site-Editor Content-width control governs it.
- **`@≤1280` sidebar squish is pre-existing** (origin/main measured identical) — not introduced here.
- no **unexpected** regression elsewhere ✓ (blog explicit widths unaffected; index/page widen
  800→832 by **deliberate site-default choice** — see §6).
- **Deploy-gated:** verified in ephemeral Playground (no live mutation). The live regression that
  rolled back #103 is reproduced-then-fixed here; a post-deploy `render-page`/computed-width check on
  the docs layout (with real content) is the gate before re-deploy.
