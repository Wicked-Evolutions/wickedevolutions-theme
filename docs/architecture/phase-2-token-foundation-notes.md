# Phase 2 — Token Foundation Notes (v0.4)

> **Phase 2 of the v0.4 native foundation** (roadmap §5.2 — role-first token model).
> **This document records the first Phase 2 *action*: the Spectral removal slice.**
> **Status:** source change (`theme.json` + `styles/knowledge.json`) + docs. No CSS, JS,
> template, asset, or PHP changes. No new design-system tokens introduced.
> **Mode:** repo-source + docs only. No WordPress calls, no live mutation, no deploy.
> **Date:** 2026-06-02 · **Branch:** `v0.4-token-foundation`
>
> Read against the canonical contract in [`AGENTS.md`](../../AGENTS.md), the
> [`v0.4-native-core-refactor-roadmap.md`](../plans/v0.4-native-core-refactor-roadmap.md),
> and the [`native-authority-spec.md`](native-authority-spec.md) (which this slice
> resolves row 7 of).

---

## 1. Why this slice is first

Phase 1 (the [native authority spec](native-authority-spec.md)) inventoried the theme
and left a set of v0.4 decisions open in §8. The **2026-06-02 decision checkpoint**
(spec §7) closed three of them. One — **Spectral (open question 4, inventory row 7)** —
resolves to a concrete, self-contained source edit with no dependency on the still-open
questions. The native-authority-spec explicitly flags it as the **only** later-band item
that *blocks* the v0.4 token work, because the knowledge variation's body type depends on
it (spec §5, "none block phases 2–4 except the Spectral asset question").

So it is taken first: a minimal, reviewable slice that removes a broken reference and
unblocks the role-token vocabulary work, without pre-empting any open decision.

## 2. What changed in this slice

The Spectral font family pointed at `assets/fonts/spectral-v13-latin-*.woff2` files that
**do not exist** in the repo (the fonts dir holds only Syne, Manrope, JetBrains Mono). Per
the spec §7 resolution — *remove for now, do not commit the missing `woff2` files* — the
Spectral references are removed and the one usage is reassigned to an existing token.

| File | Change | Rationale |
|---|---|---|
| `theme.json` | Removed the `spectral` entry from `settings.typography.fontFamilies` (was the 4th family, with five `fontFace` `src` entries to missing files). | The preset registered faces that 404; no other source references the `spectral` slug. Remaining families: `syne`, `manrope`, `jetbrains-mono`. |
| `styles/knowledge.json` | Removed the `spectral` entry from `settings.typography.fontFamilies`. | Same broken faces, mirrored into the variation. |
| `styles/knowledge.json` | `styles.typography.fontFamily` (variation **body** font): `var(--wp--preset--font-family--spectral)` → `var(--wp--preset--font-family--manrope)`. | Manrope is the base theme's body family (`theme.json` `styles.typography.fontFamily`), so the knowledge variation's body falls back to the design system's own body face rather than a now-undefined token. Headings in the variation are **unchanged** — still `var(--wp--preset--font-family--syne)`. |

**Body = Manrope, headings = Syne** was chosen over Syne-for-body because Manrope is
already the theme-wide body family; using it keeps the variation's body text on the
established body face and avoids promoting a display face (Syne) into long-form reading
copy. No repo evidence suggested Syne-for-body was safer.

This is **coherence, not expansion** (spec §6): no new colors, fonts, spacing, or surfaces
are introduced — a broken font is removed and its single usage repointed to an existing
token.

## 3. Residual `--…--spectral` references left in place (deliberately)

A repo-wide search after the edit shows two remaining references to
`var(--wp--preset--font-family--spectral)`. **Both are intentionally untouched in this
slice**; neither is in source the token model owns yet:

| Location | What it is | Why left, and where it gets resolved |
|---|---|---|
| `assets/css/theme.css:69` | `.we-site-knowledge .wp-block-post-content` rule setting body copy to the Spectral token. | **Off-limits this phase** (task constraint; spec §3.6 / §6: *no CSS cleanup before its tier's authority decision*). With the `spectral` preset gone, this `var()` is undefined → the `font-family` declaration is ignored → `.we-site-knowledge` body copy inherits the variation's body family, which this slice set to **Manrope**. So the rule degrades gracefully to the intended result; it is now inert and is cleaned up under **roadmap phase 3** (global CSS → `theme.json`, by promotion). |
| `THEME-CONFIG.md:154` | An *illustrative* code snippet in the "Body Class CSS Scoping" section, using the Spectral token as its example. | Documentation, not active source. It accurately describes the (still-present, now-inert) `theme.css:69` rule, so leaving it keeps the doc and the CSS in agreement. It should be updated together with the `theme.css` rule in phase 3. |

This means the spec's "no `var(--wp--preset--font-family--spectral)` anywhere" goal is
**met in the layers this slice owns** (`theme.json`, `styles/knowledge.json`) but **not
yet repo-wide**, by design — the last two references belong to the CSS tier whose
authority decision (phase 3) has not been made. Flagged here so the residual is not
mistaken for an oversight.

## 4. Settled user decisions carried into Phase 2

From the spec §7 decision checkpoint (2026-06-02), the canonical record this slice
operates under:

- **Theme identity — reusable, WE-primary.** The theme stays a Wicked Evolutions product
  (primary use case) but should become generic/reusable for other consumers. **Consequence
  for token work:** token naming favors role/intent names and **avoids unnecessary WE-only
  / site-IA coupling**. (This slice introduces no token names; it only removes one.)
- **Spectral — remove for now.** v0.4 ships **no** Spectral files. Implemented by this
  slice (§2). Supersedes the commit-vs-remove fork in spec row 7.
- **FluentCart — out of native-core scope.** A separate plugin/integration, handled as its
  own scoped PR; native-core work neither adds nor maintains a FluentCart token bridge in
  this band. Not touched here.
- **Light/dark — deferred (phase-4 gated).** The user has not decided whether light and
  dark are a *difference* or a *combination*. **Not touched here:** no change to
  `data-theme`, the `light-*` palette tokens, `theme-toggle.js`, or style-variation
  activation PHP.

## 5. What remains open for Phase 2

Phase 2's substance — the **role-first token vocabulary** — is **not** decided by this
slice. Removing Spectral only clears a broken reference out of the font set; the token
model decisions remain open:

- **Role token vocabulary** (spec §8 Q2, rows 1, 6) — the canonical semantic role names
  (e.g. `surface` / `surface-raised` / `text` / `text-muted` / `accent` / `border-*`), and
  whether category accents (`cat-*`) fold into the role model or stay a separate semantic
  set. **Must be agreed before any CSS migrates** (roadmap §5 ordering). Framing per §4:
  product-neutral / role-first, WE as primary consumer.
- **Repeated layout constants** (spec row 14) — sticky offset (`top:112px`), content gutter
  (`clamp(24px,5vw,56px)`), currently literals across `theme.css` / `toc.js` / templates.
  Candidate `theme.json` `custom` props; not yet promoted.
- **`wideSize` mismatch** (spec row 15) — `1200px` in `single-post.html` / `page.html`
  vs `1140px` in `theme.json` layout and `footer.html`. To be reconciled to one wide
  constant.

These are decisions/vocabulary work, not CSS deletion, and they precede the phase-3 CSS
promotion (which includes retiring the now-inert `theme.css:69` Spectral rule).

### Deferred to Phase 4 (not Phase 2)

- **Light/dark / style-variation model** (spec §8 Q1, rows 8, 9, 10, 11) — whether dark +
  light is a tier-3 style variation/global-styles mechanism vs the `[data-theme]` CSS
  toggle + JS, and which is the single source of truth. **Gated and untouched** until the
  user's light/dark decision (spec §7). No `[data-theme]` / light-token / `theme-toggle.js`
  work happens in Phase 2.

## 6. Validation performed in this run

- **JSON syntax** — `theme.json` and `styles/knowledge.json` both parse (`json.load`,
  exit 0).
- **Font sets** — both files now expose exactly `[syne, manrope, jetbrains-mono]`; knowledge
  variation body `fontFamily` = `…--manrope`, heading `fontFamily` = `…--syne`.
- **`spectral-v13`** — no references remain in source or assets (only in the spec's own
  prose at `native-authority-spec.md:269,417`, describing the original/resolved state).
- **`var(--wp--preset--font-family--spectral)`** — removed from the two source files;
  residuals only at `assets/css/theme.css:69` (off-limits) and `THEME-CONFIG.md:154` (doc
  example) — see §3.
- **`git diff --check`** — clean (no whitespace errors).

## 7. Relationship to roadmap and spec

- **Roadmap §5.2 (phase 2, role-first token model)** — this slice is the *first action*
  inside phase 2, clearing the Spectral blocker the spec flagged. It does **not** complete
  phase 2 (the role vocabulary, §5, is still open).
- **`native-authority-spec.md` row 7** — marked **resolved (this branch)** by this slice
  (see the spec's decision inventory).
- **`AGENTS.md`** — authority ladder, surgical-diff, no-deploy, and public-repo rules apply
  unchanged. CSS stays at tier 9 and is not edited before its authority decision.
