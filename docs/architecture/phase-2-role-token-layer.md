# Phase 2 — Role Token Layer (v0.4)

> **Phase 2 of the v0.4 native foundation** (roadmap §5.2 — role-first token model).
> **This document records the Phase 2 *substance*: the stable role token layer** that
> closes the role-vocabulary item left open in
> [`phase-2-token-foundation-notes.md` §5](phase-2-token-foundation-notes.md) (the
> earlier Spectral slice unblocked this; it did not decide it).
> **Status:** source + docs only. `theme.json`, `styles/knowledge.json`, and the
> `templates/`/`parts/`/`patterns/` markup that *binds* to tokens. No CSS, JS, or PHP
> changes. No new design values.
> **Mode:** repo-source + docs only. No live mutation, no deploy. One read-only Abilities
> pass (`themes/design-snapshot`) captured the deployed baseline for parity.
> **Date:** 2026-06-02 · **Branch:** `v0.4-token-foundation` · **Issue:** #93
>
> Read against [`AGENTS.md`](../../AGENTS.md) §1 (authority ladder — this is tier-2/3
> work), the [`native-authority-spec.md`](native-authority-spec.md) decision inventory
> (rows 1, 2, 6), and issue #93 acceptance.

---

## 1. What this slice does

Introduces a **stable ROLE token layer** in `theme.json` (and the `knowledge` variation)
so that saved block markup binds to **roles** (intent), not to **brand/value/category
names**. The brand/value/category tokens are **kept** but **demoted to a secondary
layer**: they remain as palette entries (named swatches and alias targets) but are no
longer the vocabulary that templates, patterns, or the design system's own styles bind
to. Scale spacing tokens are kept as-is (already portable).

This satisfies issue #93: *"role layer in theme.json; brand/value/category tokens demoted
to a second layer; visual parity."*

## 2. The two-layer token model

### Primary layer — roles (what markup binds to)

**Font-family roles** (`settings.typography.fontFamilies`, added before the name
families):

| Role slug | Resolves to | Used for |
|---|---|---|
| `heading` | `syne` (Syne) | All headings (`elements.heading`), display titles |
| `body` | `manrope` (Manrope) | Root body copy, excerpts, site title |
| `mono` | `jetbrains-mono` (JetBrains Mono) | Code, captions, eyebrows, meta, nav labels |
| `serif` | `spectral` (Spectral) | Knowledge-variation body copy (brand serif) |

**Color roles** (`settings.color.palette`). The dark base already used role slugs
(`base`, `contrast`, `secondary`, `muted`, `ghost`); this slice adds the accent roles:

| Role slug | Resolves to | Used for |
|---|---|---|
| `accent-1` | `yellow` | Primary accent — links, quote border |
| `accent-1-tint` | `yellow-tint` | Primary accent wash — quote background |
| `accent-2` | `green` | Secondary accent (role vocabulary for later CSS promotion) |
| `accent-3` | `purple` | Tertiary accent (role vocabulary for later CSS promotion) |

### Secondary layer — kept, no longer bound (untouched values)

Still present as palette/font entries, but **demoted** (nothing in markup or the design
system's styles references them directly anymore):

- **Brand/value colors:** `yellow`, `green`, `purple`, `purple-light`, and the
  `*-tint` / `*-border` variants.
- **Category accents:** `cat-module … cat-changelog` — kept as a distinct semantic set
  (they are consumed only by tier-9 `blog.css` via `--cat-accent`, which is off-limits
  this phase). Not folded into the role model.
- **Light-mode value tokens:** `light-*` — untouched (light/dark is the still-open
  phase-4 decision; spec §7/§8 Q1).
- **Font name slugs:** `syne`, `manrope`, `jetbrains-mono`, `spectral` — retained as the
  **font-registration layer** (they carry the `fontFace` bundling) and as the alias
  targets of the role families.

### Unchanged — scale layer

`settings.spacing.spacingSizes` (`10 … 80`) and the fluid `fontSizes` scale are kept
verbatim (already role/scale-portable; spec rows 2, 3).

## 3. Native mechanism (Theme Handbook grounding)

- **Role font families are alias presets.** A `fontFamilies` entry needs only
  `name` / `slug` / `fontFamily`; `fontFace` is optional and only used for bundling
  (Handbook → *Settings/Typography*). The role entries carry **no** `fontFace` (so the
  `@font-face` is registered once, by the name family) and set
  `fontFamily: "var(--wp--preset--font-family--{name-slug})"`. WordPress emits
  `--wp--preset--font-family--heading: var(--wp--preset--font-family--syne)`, which
  resolves to the name family's stack. The Handbook explicitly recommends **semantic
  slugs over name slugs** ("more future proof when switching between … style variations").
- **Role colors are alias presets.** A `palette` entry's `color` takes "a valid CSS color
  value" (Handbook → *Settings/Color*); `var(--wp--preset--color--yellow)` is valid CSS,
  so `--wp--preset--color--accent-1: var(--wp--preset--color--yellow)` resolves to the
  brand value. Markup binds via `var:preset|color|accent-1` (Handbook → *Styles/Using
  Presets*).
- **Why `var()` aliasing (not literal duplication).** One source of truth (the value lives
  once, on the name/brand token); the role points at it. It is also **variation-correct**:
  under `styles/knowledge.json`, `accent-1` automatically resolves to that variation's own
  `yellow` (`#8B6914` amber) and `body` to its own Manrope stack — the role keeps the same
  meaning while looking right in each skin. Computed CSS is byte-identical to the previous
  direct references, so parity is **by construction**.
- **Style variations replace preset arrays, so roles are mirrored in the variation.**
  A variation's `settings` overrides `theme.json` and is saved to the DB (Handbook →
  *Style Variations*); `styles/knowledge.json` already re-declares the **full** palette and
  `fontFamilies` arrays, so the role presets are **added to `knowledge.json` too** —
  otherwise they would not exist under the knowledge variation and its role bindings would
  break.

## 4. Rebindings (what now points at roles)

- **`theme.json` styles** — root `typography.fontFamily` → `body`; `elements.heading` →
  `heading`; `elements.link` + `core/quote` → `accent-1` / `accent-1-tint`; `h4`,
  `caption`, `core/code`, `core/preformatted`, `core/navigation`, `core/post-date`,
  `core/post-terms` → `mono`.
- **`styles/knowledge.json` styles** — root `typography.fontFamily` → `serif` (knowledge
  body is the brand serif); `elements.heading` → `heading`; `elements.link` +
  `core/quote` → `accent-1` / `accent-1-tint`.
- **Markup** (`templates/`, `parts/`, `patterns/`) — every saved font binding rebinds
  name-slug → role-slug across all three serialization forms WordPress uses:
  the block-comment attribute (`"fontFamily":"jetbrains-mono"` → `"mono"`), the
  preset-reference style (`var:preset|font-family|jetbrains-mono` → `…|mono`), the
  rendered inline style (`var(--wp--preset--font-family--jetbrains-mono)` → `…--mono`),
  **and** the rendered class (`has-jetbrains-mono-font-family` → `has-mono-font-family`)
  so each block-comment and its serialized HTML stay consistent (no editor block-validation
  mismatch). `syne` → `heading`, `manrope` → `body`, `jetbrains-mono` → `mono`. No color
  tokens are bound in markup (brand colors were already CSS-only), so no color rebinds were
  needed there.

## 5. Parity verification

- **JSON validity** — `theme.json` and `styles/knowledge.json` both parse (`json.load`,
  exit 0).
- **No name/brand bindings remain** — repo-wide grep over `templates/`/`parts/`/
  `patterns/` and the `styles` blocks of both JSON files returns **zero** references to
  `syne`/`manrope`/`jetbrains-mono`/`spectral` or `yellow`/`green`/`purple`/`cat-*` as
  *bindings* (they survive only as palette/font **definitions** and as role alias targets).
- **Baseline captured via Abilities (read-only).** `themes/design-snapshot` on
  `wickedevolutions` (deployed v0.3.1) confirms the exact values the roles must preserve:
  `yellow #FFEE58`, `yellow-tint rgba(255,238,88,0.07)`, `green #22c55e`, `purple #7c3aed`;
  `syne Syne, sans-serif`, `manrope Manrope, system-ui, sans-serif`,
  `jetbrains-mono 'JetBrains Mono', monospace`, `spectral 'Spectral', Georgia, …`. Each
  role added is a `var()` alias of exactly these, so computed CSS is unchanged.
- **Deploy-gated step (not done, out of scope).** Verifying the *post-change* effective
  `theme.json` on the live site requires a deploy; this slice makes no live mutation. The
  parity argument above is the available verification for an undeployed source change. A
  post-deploy `themes/get-theme-json` + `content/render-page` diff is the follow-up if/when
  a deploy is authorized.

## 6. Scope boundaries (explicitly not done)

- **No CSS / JS / PHP edits.** `assets/css/theme.css` (incl. `[data-theme]` blocks,
  `.we-site-knowledge … --spectral`) and `blog.css` `--cat-accent` are tier-9 and remain
  off-limits this phase (spec §6).
- **No content-width work** — `wideSize` 1200/1140 reconciliation is Phase 4 / #95.
- **No light/dark work** — `light-*` tokens, `[data-theme]`, `theme-toggle.js`,
  variation-activation PHP are untouched (phase-4-gated; spec §7).
- **No theme rename, no plugin move.** One theme serving multisite MAIN + subsites.
- **Pre-existing serialization quirks left as-is.** Two `core/group` blocks
  (`category.html`, `single-post.html` breadcrumb) already stored their `<div>` without the
  font-family artifact the comment implies; this slice only swapped the comment slug 1:1
  (behaviour-identical) and did not "fix" the pre-existing gap (surgical-diff discipline).

## 7. Relationship to roadmap / spec / issue

- **Roadmap §5.2 (phase 2, role-first token model)** — this is the substantive phase-2
  deliverable: the token **vocabulary**, decided before any CSS migrates into it (phase 3).
- **`native-authority-spec.md`** — advances rows 1 (palette) and 2 (type) toward role
  semantics and gives row 6 (border tokens) a role vocabulary to resolve into later;
  answers spec §8 **Q2** (role names = `heading`/`body`/`mono`/`serif`, `accent-*`;
  `cat-*` stays a separate secondary set).
- **Issue #93 acceptance** — role layer in `theme.json` ✓; brand/value/category demoted to
  a second layer ✓; visual parity (by construction, baseline-confirmed) ✓; Abilities
  effective-`theme.json` re-check is deploy-gated (see §5).
