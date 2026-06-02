---
ijfw_version: 1.3.2
ijfw_schema: 1
type: software
primary_type: software
secondary_types: []
confidence: 0.732
detected_at: 2026-06-02T06:42:31.813Z
signals:
  - kind: dir_design
    weight: 0.4
    name: assets
  - kind: file_extension_ratio
    weight: 0.7
    domain: software
    ratio: 1
    count: 15
---
# AGENTS.md — Wicked Evolutions Theme

This file follows the open AGENTS.md spec (https://agents.md/) and is the
**canonical, cross-agent operating contract** for this repository. It is
tool-agnostic: every AI agent — Claude Code, Gemini, Codex, Cursor, Windsurf,
Copilot, and any future tool — follows the rules here. Platform-specific files
(CLAUDE.md, GEMINI.md, codex/AGENTS.md, .cursorrules, .windsurfrules,
copilot-instructions.md) are thin adapters that point back to this file and add
only that tool's own collaboration mechanics.

## What this repository is

A **public** WordPress **block theme** for the Wicked Evolutions ecosystem:
native Gutenberg, full-site editing (FSE), dark-first. Build to the highest
native WordPress/Gutenberg standard. Live WordPress data and runtime operations
go through the Abilities MCP first ("Abilities-first"). Because the repo is
public, treat everything in it as world-readable.

## 1. WordPress-native authority order

When deciding how to build something, follow this order and stop at the first
that resolves it. Do not reach down the list to avoid learning the native
mechanism higher up.

1. **Core / editor UI & saved block attributes** — configure in the editor; let core blocks and `supports` own layout and styling via saved attributes. Prefer core over custom.
2. **theme.json** — global settings, presets, custom properties.
3. **Global styles & style variations** (`styles/`) — site-wide theming and named variations.
4. **Templates & template parts** (`templates/`, `parts/`) — HTML block markup.
5. **Patterns & block style variations** (`patterns/`) — composition and per-block style options.
6. **Dynamic blocks & render callbacks** — server-rendered block output when markup must be computed.
7. **Theme PHP** (`functions.php`) — registration, filters, server-side logic the above cannot express.
8. **Plugins / Abilities** — runtime/data operations and capabilities beyond the theme (see §2).
9. **Custom CSS/JS** (`assets/`) — last-mile only, for effects; never for layout that block settings can own.

**Reference for the mechanisms above:** the **WordPress Theme Handbook** is the authority for *how* each layer of this ladder works. Local agents read the offline mirror through the **mcp-obsidian** server:

```
# read a Handbook page
read_file(vault: "Wordpress Developers Handbooks",
          path: "Wordpress Developer Handbooks/Theme Handbook/Global Settings and Styles (theme.json)/Style Variations")

# or search the Handbook
search_content(vault: "Wordpress Developers Handbooks", query: "block stylesheets")
```

Public canonical: https://developer.wordpress.org/themes/. Consult it before improvising a native mechanism.

## Sources of truth — design vs. native mechanism vs. current state

Four sources, each authoritative for one thing only. Never let one stand in for another — in particular, **the live site is not the design authority**; it may be exactly what is being corrected.

| Source | Authoritative for | Where |
|---|---|---|
| **Design prototypes** | Brand & visual design intent — what the theme must reproduce natively | `Wicked-Evolutions/wickedevolutions-prototypes` · https://wicked-evolutions.github.io/wickedevolutions-prototypes/ (e.g. `docs-prototype/`) |
| **WordPress Theme Handbook** | The native mechanism — *how* to build each layer in §1 | mcp-obsidian vault "Wordpress Developers Handbooks" · https://developer.wordpress.org/themes/ |
| **Live site** (Abilities MCP) | Current runtime state — what is deployed, what would break. **Not** a design authority. | Abilities MCP on the target site |
| **This repo** | What the theme source currently declares | the working branch |

**Before removing or repointing a brand/config asset** (fonts, palettes, enqueue rules) — especially headless/print-mode agents: the design **prototypes** decide whether an asset is intended, not the live site and not a missing file. A referenced-but-missing asset is a gap to fix *toward the prototype*, not proof the asset is unwanted. (The Spectral-font lesson: Spectral is brand typography in the prototype — restore it, don't delete it.) A PR comment is not a durable record; if a lesson emerges, file an issue and record the rule here.


## 2. Abilities MCP first

All **live WordPress data/runtime operations** go through the Abilities MCP
(`mcp__wordpress__*`) — content, taxonomies, menus, media, settings, plugins,
themes, users. No SSH or ad-hoc REST fallback for an operation an ability covers.

- Boot the bridge via `mcp-adapter/get-started`, then follow `next_action`.
- Filesystem edits in *this repo* (theme source) are normal local file work, not WordPress operations.
- **If Abilities cannot do the operation:** STOP → explain what's missing → ask the user → log it in [`docs/operations/abilities-gap-log.md`](docs/operations/abilities-gap-log.md). Gaps are the product roadmap; do not silently route around them.

## 3. Public-repo security — no secrets

This repository is public. Never commit, read aloud, echo, or paste into
commits/code/chat:

- Secrets or credentials: `.env*`, `*.key`/`*.pem`/`*.crt`/`*.p12`/`*.pfx`, API tokens, passwords.
- Server/tooling config: `wp-config.php`, `.mcp.json`, `.claude/settings.local.json`, `.hermes/`, local IJFW state in `.ijfw/`.
- Database dumps (`*.sql`), local backups (`.backups/`), logs.

These are covered by `.gitignore`. If a secret is found tracked, stop and flag it.

## 4. No deploy or live mutation without explicit instruction

Do not deploy, publish, push, or mutate any live WordPress state (production or
otherwise) unless the user explicitly instructs that specific action. Local file
work in this repo is not a live mutation. Approval for one operation does not
extend to the next.

## 5. Working discipline

- **Protect pre-existing work.** Do not read, edit, stage, or revert files that are already dirty or unrelated to your task. Leave another author's uncommitted changes untouched.
- **Surgical diffs.** Keep changes minimal and scoped to the task; match surrounding code style. No drive-by refactors.
- **Verify by tools, not assumption.** Confirm state with reads/searches/commands before claiming it. Report outcomes faithfully — if something failed or was skipped, say so with the evidence.
- **No fabricated paths or results.** Never invent file paths, command output, ability names, or verification you did not actually run.
- **Stop when confused.** If the request is ambiguous, contradicts the repo, or a needed ability/tool is missing, stop and ask rather than guessing.

## 6. Git workflow

- Work on a feature/chore branch; never commit directly to `main`.
- Stage, commit, or push **only when the user explicitly asks**.
- Conventional, descriptive commit messages.

## IJFW-managed regions

Five IJFW-managed regions live in this file. Content outside the markers is
yours — IJFW will never touch it; content inside the markers is generated and
should not be hand-edited.

| Region | Purpose |
|---|---|
| MEMORY | Project memory recalled from `.ijfw/memory/` |
| ROUTING | Platform skill-routing rules |
| AGENTS | Registered agent roster |
| BLACKBOARD | Multi-CLI orchestration scratchpad (Pillar B) |
| DISCIPLINE | Per-domain discipline rules (code \| narrative \| business \| design \| research) |

<!-- IJFW-MEMORY-START -->
Project memory at .ijfw/memory/. Call `ijfw_memory_prelude` for full context.
<!-- IJFW-MEMORY-END -->

<!-- IJFW-ROUTING-START -->
<!-- IJFW-ROUTING-END -->

<!-- IJFW-AGENTS-START -->
No project agents yet. Run `ijfw team` to set them up.
<!-- IJFW-AGENTS-END -->

<!-- IJFW-BLACKBOARD-START -->
<!-- Reserved for Pillar B multi-CLI orchestration. Empty in alpha. -->
<!-- IJFW-BLACKBOARD-END -->

<!-- IJFW-DISCIPLINE-START -->
<!-- IJFW-DISCIPLINE-END -->
