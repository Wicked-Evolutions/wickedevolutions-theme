# Decision — Orphan Landing Enqueue Rule (`the-mirror-landing-page`)

**Date:** 2026-06-02
**Mode:** Read-only / inquiry. No WordPress state mutated; no deploy. Source/docs only.
**Related:** [`abilities-inspection-2026-06-02.md`](abilities-inspection-2026-06-02.md) · [`abilities-gap-log.md`](abilities-gap-log.md)

## Summary

The live `wickedevolutions` site carries an **ability-managed enqueue rule with no backing
file and no matching template or page**. The ladder-correct fix is to **retire (remove) the
rule**, not to manufacture a file or template to satisfy it. That removal is **not performed
in this pass**: the site is in read-only/inquiry mode and **no Abilities operation to
remove or reconcile an enqueue rule is exposed**. The missing operation is recorded as a
product gap, not treated as client/AI confusion.

## Evidence

All gathered via read-only Abilities calls and local repo search.

- **`themes/list-enqueued-assets`** — exactly one rule:
  - `handle` = `the-mirror-landing-page`
  - `src` = `assets/css/landing-page.css`
  - `type` = `style`, `deps` = `[the-mirror-landing]`, `version` = `filemtime`
  - `condition` = `{ type: template, value: page-landing }`
  - `added_by` = `ability`, `added_at` = `2026-03-16T07:23:04+00:00`
  - **`file_exists` = `false`**
- **`themes/list-mods`** — active theme `wickedevolutions-theme`; mods include
  `we_enable_blog_templates=1`, `we_enable_blog_css=1`, and nav menu locations
  `topbar` / `tabstrip`. **No landing / `page-landing` mod** is present.
- **`content/list-structure`** (pages, `post_status=any`) — 17 pages returned. **No page
  with slug `page-landing`.** Landing-ish pages exist (e.g. *Trinity Landing — Block
  Learning Test*, *Abilities for AI*), but none carries a `page-landing` template signal.
- **Abilities discovery** (themes category) — only **read** abilities are exposed:
  `themes/list`, `themes/get-active`, `themes/list-mods`, `themes/get-mod`,
  `themes/get-theme-json`, `themes/design-snapshot`, `themes/list-enqueued-assets`.
  A search for enqueue / enqueued-asset operations returned **no write or remove ability**.
- **Local repo search** — no `assets/css/landing-page.css`, no `templates/page-landing.html`,
  and no source references to `page-landing` / `landing-page` / `the-mirror-landing` outside
  `docs/operations` and `docs/plans`.

## Interpretation

The rule is an **orphan**: nothing in the theme source declares it, no template named
`page-landing` exists to fire its condition, no page resolves to that template, and the file
it points at is absent. It was added at runtime by an ability (`added_at` 2026-03-16) and was
never reflected back into the tracked theme source. It is dead weight that, on a matching
template, would request a 404 stylesheet.

## Decision

1. **Do not add `assets/css/landing-page.css` as a stub.** Creating a file to satisfy a rule
   that nothing in the theme intends would invert the authority order — source would be made
   to follow a stray runtime rule rather than the reverse.
2. **Do not add `templates/page-landing.html` speculatively.** There is no product intent for
   a landing template in the tracked source; inventing one to "explain" the orphan rule is
   speculation, not reconciliation.
3. **Ladder-correct branch: retire / remove the ability-managed enqueue rule** so live state
   matches the theme source (which declares no such asset).
4. **Live mutation is not performed in this pass.** The site is read-only/inquiry, and there
   is **no exposed Abilities operation** to remove or reconcile an enqueue rule. Removal is
   deferred until (a) read-only mode is lifted and (b) a remove/reconcile-enqueue ability
   exists. The missing ability is logged in [`abilities-gap-log.md`](abilities-gap-log.md).

## Product signal

The absence of a remove/reconcile-enqueue ability is **product signal, not confusion**.
Abilities can *add* an ability-managed enqueue rule (this orphan is proof) and can *list* it,
but cannot *remove* or *repoint* it. That is a one-way door: runtime can create theme-asset
state the Abilities surface can never clean up. Closing this gap — a
`themes/remove-enqueued-asset` (or `themes/reconcile-enqueued-assets`) operation — is the
roadmap item that unblocks the ladder-correct fix above.

## Next action (when read-only is lifted and the ability exists)

- Remove the `the-mirror-landing-page` enqueue rule via the (to-be-built) remove/reconcile
  ability, then re-run `themes/list-enqueued-assets` to confirm zero orphan rules.
- No source change accompanies the removal — the theme already declares no landing asset.
