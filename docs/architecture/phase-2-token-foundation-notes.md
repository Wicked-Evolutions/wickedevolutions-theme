# Phase 2 — Token Foundation Notes (v0.4)

> **Phase 2 of the v0.4 native foundation** (roadmap §5.2 — role-first token model).
> **This document records the first Phase 2 *action*: the Spectral font-parity slice.**
> **Status:** source restore (`theme.json` + `styles/knowledge.json` restored from
> `origin/v0.4-native-foundation`) + font assets added
> (`assets/fonts/spectral-v13-latin-*.woff2`) + docs. No CSS, JS, template, or PHP
> changes. No new design-system tokens introduced.
> **Mode:** repo-source + docs only. No WordPress calls, no live mutation, no deploy.
> **Date:** 2026-06-02 · **Branch:** `v0.4-token-foundation`
>
> Read against the canonical contract in [`AGENTS.md`](../../AGENTS.md), the
> [`v0.4-native-core-refactor-roadmap.md`](../plans/v0.4-native-core-refactor-roadmap.md),
> and the [`native-authority-spec.md`](native-authority-spec.md) (which this slice
> resolves row 7 of).
>
> **Correction (2026-06-02, Hermes/user checkpoint).** An earlier draft of this slice
> proposed *removing* Spectral. That was wrong. **Spectral is live on
> wickedevolutions.com and is part of the Wicked Evolutions brand typography.** The live
> site is the authoritative visual/brand evidence when repo and live diverge
> (`AGENTS.md`). This slice now **preserves** Spectral and restores repo↔live font-asset
> parity by committing the bundled font files the theme already references.

---

## 1. Why this slice is first

Phase 1 (the [native authority spec](native-authority-spec.md)) inventoried the theme
and left a set of v0.4 decisions open in §8. The **2026-06-02 decision checkpoint**
(spec §7) closed three of them. One — **Spectral (open question 4, inventory row 7)** —
resolves to a concrete, self-contained source/asset action with no dependency on the
still-open token decisions. The native-authority-spec explicitly flags it as the
**only** later-band item that *blocks* the v0.4 token work, because the knowledge
variation's body type depends on it (spec §5, "none block phases 2–4 except the Spectral
asset question").

So it is taken first: a minimal, reviewable slice that restores a brand font's missing
assets and unblocks the role-token vocabulary work, without pre-empting any open
decision.

## 2. What changed in this slice

**Root cause — a repo↔live asset gap, not a stale font.** Spectral's `fontFace` `src`
entries in `theme.json` and `styles/knowledge.json` point at
`assets/fonts/spectral-v13-latin-*.woff2`. The live wickedevolutions.com site ships those
bundled files and renders Spectral correctly; the local repo working tree was simply
**missing the asset files** (the fonts dir held only Syne, Manrope, JetBrains Mono). The
defect was that the repo had drifted from live — not that the font reference was stale or
removable.

**Resolution — preserve Spectral, restore parity.** `theme.json` and
`styles/knowledge.json` are restored from `origin/v0.4-native-foundation` (keeping the
Spectral family), and the five bundled `woff2` files are added to `assets/fonts/`.

| File | Change | Rationale |
|---|---|---|
| `theme.json` | Restored from `origin/v0.4-native-foundation` — keeps the `spectral` entry in `settings.typography.fontFamilies` (4th family, with five `fontFace` faces: 300 / 400 / 400-italic / 500 / 600). | Spectral is brand typography and the live site is authoritative. Families remain `syne`, `manrope`, `jetbrains-mono`, `spectral` (`theme.json:58-67`). |
| `styles/knowledge.json` | Restored — keeps the `spectral` family and the variation **body** `fontFamily` = `var(--wp--preset--font-family--spectral)`; headings stay `var(--wp--preset--font-family--syne)`. | The knowledge variation's body face is Spectral by brand design; matches live (`styles/knowledge.json:46-51, 69, 77`). |
| `assets/fonts/spectral-v13-latin-{300,regular,italic,500,600}.woff2` | **Added** (the bundled files the two JSONs already reference, taken from live). | Closes the repo↔live gap so `file:./assets/fonts/*.woff2` resolves in-repo exactly as it does on the live site. |

**Why committing the files is the native-correct fix (handbook).** `fontFace` is the
`theme.json` mechanism for **bundled web fonts**; its `src` may use
`file:./assets/fonts/*.woff2` paths resolved **relative to `theme.json`**; each family
generates a `--wp--preset--font-family--{slug}` custom property; and style variations
live in `/styles/*.json`. So the Spectral references in `theme.json` /
`styles/knowledge.json` are valid native declarations — they only ever needed the backing
files present. See the **Source protocol** and handbook citations in
[`native-authority-spec.md` §10](native-authority-spec.md).

This is **coherence/parity, not expansion** (spec §6): no new colors, fonts, spacing, or
surfaces are introduced — an existing brand font is kept and its missing asset files are
restored to the repo.

## 3. `--…--spectral` references across the repo (now all backed)

A repo-wide search shows `var(--wp--preset--font-family--spectral)` used in the variation
body type and in CSS scoping. With Spectral **preserved** and its assets present, every
reference now resolves to a real, loadable font:

| Location | What it is | Status |
|---|---|---|
| `styles/knowledge.json:69` (variation body `fontFamily`) | Knowledge variation body copy = Spectral token. | **Valid** — backed by the restored `spectral` family and the added `woff2` files. |
| `assets/css/theme.css:69` | `.we-site-knowledge .wp-block-post-content` rule setting body copy to the Spectral token. | **Valid and intended** — matches the live brand rendering. **Off-limits this phase** (no CSS edits; spec §3.6 / §6); left exactly as-is. |
| `THEME-CONFIG.md:154` | Illustrative snippet using the Spectral token in the "Body Class CSS Scoping" section. | **Accurate** — documents the live/active rule; left as-is. |

This is the inverse of the earlier (removal) draft: the references are correct, and this
slice makes them *resolvable* by supplying the assets — rather than leaving an undefined
token to clean up. No CSS is edited here; CSS-tier authority decisions remain phase 3.

## 4. Settled user decisions carried into Phase 2

From the spec §7 decision checkpoint (2026-06-02), **as corrected 2026-06-02 by the
Hermes/user checkpoint**, the canonical record this slice operates under:

- **Theme identity — reusable, WE-primary.** The theme stays a Wicked Evolutions product
  (primary use case) but should become generic/reusable for other consumers. **Consequence
  for token work:** token naming favors role/intent names and **avoids unnecessary WE-only
  / site-IA coupling**. (This slice introduces no token names.)
- **Spectral — preserve (brand typography).** Spectral is live on wickedevolutions.com
  and part of the WE brand. v0.4 **keeps** Spectral and **commits the bundled `woff2`
  files** the theme already references, restoring repo↔live parity. This **supersedes the
  earlier "remove for now" direction**, which was based on the missing-files *symptom*
  rather than the live brand *evidence*. Implemented by this slice (§2).
- **FluentCart — out of native-core scope.** A separate plugin/integration, handled as its
  own scoped PR; native-core work neither adds nor maintains a FluentCart token bridge in
  this band. Not touched here.
- **Light/dark — deferred (phase-4 gated).** The user has not decided whether light and
  dark are a *difference* or a *combination*. **Not touched here:** no change to
  `data-theme`, the `light-*` palette tokens, `theme-toggle.js`, or style-variation
  activation PHP.

## 5. What remains open for Phase 2

Phase 2's substance — the **role-first token vocabulary** — is **not** decided by this
slice. Restoring Spectral only closes a font-asset gap; the token model decisions remain
open:

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
promotion.

### Deferred to Phase 4 (not Phase 2)

- **Light/dark / style-variation model** (spec §8 Q1, rows 8, 9, 10, 11) — whether dark +
  light is a tier-3 style variation/global-styles mechanism vs the `[data-theme]` CSS
  toggle + JS, and which is the single source of truth. **Gated and untouched** until the
  user's light/dark decision (spec §7). No `[data-theme]` / light-token / `theme-toggle.js`
  work happens in Phase 2.

## 6. Validation performed in this run

- **JSON syntax** — `theme.json` and `styles/knowledge.json` both parse (`json.load`,
  exit 0).
- **Font sets** — both files now expose exactly `[syne, manrope, jetbrains-mono, spectral]`;
  knowledge variation body `fontFamily` = `…--spectral`, heading `fontFamily` = `…--syne`.
- **Asset parity** — all five `assets/fonts/spectral-v13-latin-*.woff2` files referenced
  by the two JSONs now exist in `assets/fonts/` (alongside the three variable fonts).
  `git status`: `theme.json` + `styles/knowledge.json` modified (restored from
  `origin/v0.4-native-foundation`), five Spectral `woff2` files new/untracked.
- **No CSS / JS / template / PHP changes**; no WordPress calls; no live mutation; no
  deploy. Spectral is **not** removed anywhere.

## 7. Relationship to roadmap and spec

- **Roadmap §5.2 (phase 2, role-first token model)** — this slice is the *first action*
  inside phase 2, restoring the Spectral brand font + assets the spec flagged as the
  blocker. It does **not** complete phase 2 (the role vocabulary, §5, is still open).
- **`native-authority-spec.md` row 7** — marked **resolved by preservation + asset
  commit** (this branch); see the spec's decision inventory.
- **`AGENTS.md`** — authority ladder, surgical-diff, no-deploy, and public-repo rules apply
  unchanged. The **live site is authoritative** visual/brand evidence when repo and live
  differ. CSS stays at tier 9 and is not edited.
