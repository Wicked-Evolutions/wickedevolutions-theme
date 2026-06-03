# Phase 4 (slice 1) — content-width 832 (issue #95)

> **Branch:** `v0.4-content-width` · **Date:** 2026-06-02 · Light/dark (#97) and v0.5 out.
> **Locked target:** docs 3-column middle column = **832px (52rem)** on desktop, **single-sourced
> in `theme.json` `settings.layout.contentSize`**, user-editable via Site Editor → Styles →
> Layout → **Content width**; the `.we-content-col { 800px !important }` CSS authority removed.
> **Outcome:** achieved natively + **Playground-measured**; the readable docs column is now a pure
> function of `contentSize` (proven: editing it 832→600 moves the column to 600). No deploy.

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

**Rejected approach — fix the column to the content-size var (`flex-basis/max-width:
var(--…--content-size); flex-shrink:0`):** gave readable text = 832, but the column became 928
(832 + 96 padding), overflowing the band → **sidebar/toc shrank to 179/221** (regression). A
fixed middle also **squished** the sidebars on < ~1500 screens. Wrong: it re-hardcodes a width.

**Chosen approach — content-col *fills*, `post-content` constrained inherit governs:** remove the
`.we-content-col` width entirely (it fills via the core column default); the readable width is
capped by `post-content`'s constrained `contentSize` (832). The fixed sidebars (260/240) are
preserved (the flexible content-col absorbs the slack); the band is widened so 832 can be reached.

## 4. The changes (4 files, value = single-source)

| File | Change |
|---|---|
| `theme.json` | `settings.layout.contentSize` **800 → 832** (the single source; `wideSize` stays 1140 site-default). |
| `assets/css/theme.css` | `.we-content-col` loses `flex-basis:800!important; max-width:800!important; flex-grow:0!important` → **`{ padding: 20px 48px 80px }`** only. The column now fills; no hardcoded width, no `!important`. |
| `templates/single-post.html` | `post-content` `contentSize:"800px"` → **inherit** (`{type:constrained}`); docs band `main` `wideSize` **1200 → 1500** (per-template) so the 3-col band can fit an 832 readable column alongside the 260 sidebar + 240 toc (+ their padding). |
| `templates/front-page-knowledge.html` | `post-content` `contentSize:"800px"` → **inherit**. Its 2-col band (`wideSize` 1200) already fits 832 (no toc), so no `wideSize` change. |

`wideSize` decision (#95): the **site default** `wideSize` stays `1140` in `theme.json`; the docs
band width is a **per-template** `wideSize` (single-post 1500), which the spec explicitly sanctions
("per-template constrained-layout setting, still native"). Non-docs/blog templates untouched.

## 5. Playground evidence (computed widths, playwright/chromium)

**single-post (docs), contentSize 832, wideSize 1500 — readable text caps at 832:**

| viewport | readable | sidebar | toc | note |
|---|---|---|---|---|
| 1920 | **832** | 261 | 296 | full 3-col, capped at contentSize |
| 1600 | **832** | 261 | 296 | capped at contentSize |
| 1440 | 675 | 261 | 296 | graceful (band viewport-limited) |
| 1280 | **832** | 261 | hidden | toc hides `@≤1280`, content fills |
| 1000 | **832** | hidden | hidden | sidebar hides `@≤1024` |
| 600 | 508 | hidden | hidden | stacked, fills (`<832`) |

Readable **never exceeds 832** (the `contentSize` cap) and degrades gracefully; sidebar/toc keep
their intended 260/240 (un-shrunk).

**Editability — the control governs it (decisive):** setting `contentSize` **600px** moved the
readable column to **exactly 600** (was 832) with sidebar/toc unchanged. The readable docs column
is a pure function of `settings.layout.contentSize`, which is what the Site Editor Content-width
control edits.

## 6. Regression scope (intended single-source consequence)

`contentSize` is the **site default**, so changing 800→832 widens every template whose
`post-content` *inherits* it: the docs (single-post, front-page-knowledge → 832 via the band) **and
`index.html` + `page.html`** (their `post-content {constrained}` → 832, was 800). **Blog templates
keep their explicit widths** (`single-post-blog` 680, `page-series` 520/680, `category-blog` 680) —
unaffected. This is the deliberate single-source tradeoff: one editable default (832) for all
non-blog content. If index/page must stay 800, give them an explicit `contentSize` (still native) —
flagged for the design pass.

## 7. Honest caveats (for the design pass)

- **832 needs a wide viewport.** With the current sidebar (260) + toc (240, +56 own padding) +
  content padding (96) overhead (~653px), the 3-col band must be ≥ ~1485px for the readable column
  to reach 832 — i.e. **~1600px viewport**. Below that the column is narrower (graceful). On a
  1440 laptop it is ~675. If 832 is wanted at 1440, the *design pass* must narrow the
  sidebars/padding — out of scope here. (832 is interim per the issue.)
- **`wideSize 1500` bounds the control's upper range.** The docs band caps how wide the
  Content-width control can push the readable column (~847 at wideSize 1500); pushing `contentSize`
  far above 832 would also need the docs `wideSize` raised. The control governs the realistic range
  (and any decrease) fully — proven.
- **Toc-visible mid-range dip (1281–1599):** with the toc shown but the band viewport-limited, the
  readable column is < 832 (e.g. 675 @1440), recovering to 832 at ≥1600. Inherent to a fixed-toc +
  flexible-content layout; readable is ≥ the old baseline (644) everywhere.

## 8. Verification summary (task checklist)

- visual parity **except the intended wider docs column** ✓ (readable 644→832 on wide; sidebar/toc
  preserved; responsive breakpoints intact).
- Site Editor **Content width control governs** the docs column ✓ (contentSize 600 → readable 600).
- no **unexpected** regression elsewhere ✓ (blog explicit widths unaffected; index/page widen
  800→832 by **deliberate site-default choice** — see §6).
- **Deploy-gated:** verified in ephemeral Playground (no live mutation). Effective live check is a
  post-deploy `design-snapshot` + `render-page` compare when authorized.
