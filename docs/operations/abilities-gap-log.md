# Abilities Gap Log

## Purpose

Track operations that **could not** be completed through the WordPress Abilities MCP (`mcp__wordpress__*`), or that required a non-ability workaround. Per the Abilities-first contract in [`AGENTS.md §2`](../../AGENTS.md), every such gap is recorded here. Gaps are the product roadmap — this log is the input to closing them.

## Rules

- Log a gap when an Abilities call **fails**, an ability **does not exist** for a needed operation, or a workaround (SSH, raw REST, manual UI) was required.
- One row per distinct gap. If the same gap recurs, update its row rather than adding duplicates.
- Record only **observed** events. Do not invent or speculate failed calls.
- When a gap is closed (ability added/fixed), set **Status** to `Resolved` and note how.

## Log

| Date | Operation attempted | Site key | Ability/tool attempted | Result | Gap | Fallback used | Status |
|------|---------------------|----------|------------------------|--------|-----|---------------|--------|
| 2026-06-02 | Batch-read theme/menu/pattern abilities in one call (`themes/get-active`, `themes/list-mods`, `themes/design-snapshot`, `menus/list-locations`, `patterns/list`) | wickedevolutions | `mcp_adapter_batch_execute` | All names returned `Tool not found: ...`, despite the same names being valid (confirmed via discovery) and executing successfully one-by-one | Batch executor does not resolve ability names that individual execution resolves — batch path cannot run otherwise-valid abilities | Individual `mcp_adapter_execute_ability` calls (each succeeded) | Open |
| 2026-06-02 | Remove / reconcile the orphan ability-managed enqueue rule `the-mirror-landing-page` (src `assets/css/landing-page.css`, condition template `page-landing`, `added_by=ability`, `file_exists=false`) so live state matches theme source | wickedevolutions | Discovery in themes category — searched for an enqueue/enqueued-asset write or remove operation | No such ability exists. Themes category exposes only read abilities (`themes/list`, `get-active`, `list-mods`, `get-mod`, `get-theme-json`, `design-snapshot`, `list-enqueued-assets`); none removes or repoints an enqueue rule | Abilities can list ability-managed enqueue rules but cannot remove/repoint them, leaving no Abilities-first cleanup path for orphan runtime asset state | None — removal deferred (site is read-only/inquiry and no remove/reconcile-enqueue ability is exposed). Decision recorded in [`orphan-landing-enqueue-2026-06-02.md`](orphan-landing-enqueue-2026-06-02.md) | Open |

<!--
Row template:
| 2026-06-02 | Short description of the operation | site key (e.g. wickedevolutions.com) | ability name or tool | What happened | Why it could not be done via Abilities | What was done instead (or "none") | Open / Resolved |
-->

## Track B — v0.4 theme deploy to WE (2026-06-02)

Full Phase 2 theme change (26 files) deployed to `wickedevolutions` (production multisite) **via Abilities only — no SSH fallback needed.**

| Date | Operation | Site | Ability/path used | Result | Gap | Status |
|------|-----------|------|-------------------|--------|-----|--------|
| 2026-06-02 | Deploy a multi-file theme change (26 files) to the active theme | wickedevolutions | 1× `filesystem/write-file` (theme.json) + 25× `filesystem/fetch-remote` (markup + fonts) | Success, file-by-file | **No atomic/transactional theme deploy or directory-sync ability.** A theme change = N individual filesystem calls; no single "deploy/sync theme" op (batch path unusable per adapter #104). Ergonomics gap. | Open (enhancement) |
| 2026-06-02 | Write theme.json / templates / parts / patterns | wickedevolutions | `theme/update-asset` (rejected) → `filesystem/write-file` | Resolved | `theme/update-asset` is scoped to `assets/` + css/js/json/md only — cannot write theme root (theme.json) or templates/parts/patterns (.html/.php). Use `filesystem/write-file` (ABSPATH-relative). | Resolved (use write-file) |
| 2026-06-02 | Deploy 5× binary fonts (~22KB each .woff2) | wickedevolutions | `filesystem/fetch-remote` from public raw URLs | Success, server-side | Prior gap (write-binary >15KB through-context) is bypassed: `fetch-remote` pulls binary server-side from a published URL. **This is the viable Abilities theme-deploy mechanism.** | Resolved (fetch-remote) |
