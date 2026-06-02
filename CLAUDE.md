# CLAUDE.md — Wicked Evolutions Theme

Operating contract for AI agents working in this repository. This is a **public** WordPress block theme for the Wicked Evolutions ecosystem (native Gutenberg, full-site editing, dark-first).

---

## 1. Technical authority order

Hold to the **highest native WordPress/Gutenberg standard**. When deciding how to build something, follow this order and stop at the first that resolves it:

1. **Core / editor UI & saved block attributes** — configure in the editor; let core blocks and `supports` own layout and styling via saved attributes. Prefer core over custom.
2. **theme.json** — global settings, presets, custom properties.
3. **Global styles & style variations** (`styles/`) — site-wide theming and named variations.
4. **Templates & template parts** (`templates/`, `parts/`) — HTML block markup.
5. **Patterns & block style variations** (`patterns/`) — composition and per-block style options.
6. **Dynamic blocks & render callbacks** — server-rendered block output when markup must be computed.
7. **Theme PHP** (`functions.php`) — registration, filters, server-side logic the above cannot express.
8. **Plugins / Abilities** — runtime/data operations and capabilities beyond the theme (see §2).
9. **Custom CSS/JS** (`assets/`) — last-mile only, for effects; never for layout that block settings can own.

Do not reach down the list to avoid learning the native mechanism higher up.

## 2. Abilities MCP first

All **WordPress data/runtime operations** go through the Abilities MCP (`mcp__wordpress__*`) — content, taxonomies, menus, media, settings, plugins, themes, users. No SSH or ad-hoc REST fallback for operations an ability covers.

- Boot the bridge via `mcp-adapter/get-started`, then follow `next_action`.
- Filesystem edits in *this repo* (theme source) are normal local file work, not WordPress operations.

**If Abilities cannot do the operation:** STOP → explain what's missing → ask the user → log it in [`docs/operations/abilities-gap-log.md`](docs/operations/abilities-gap-log.md). Gaps are the product roadmap; do not silently route around them.

## 3. Public-repo security

This repository is public. Never commit:

- Secrets or credentials: `.env*`, `*.key`/`*.pem`/`*.crt`/`*.p12`/`*.pfx`, API tokens, passwords.
- Server config: `wp-config.php`, `.mcp.json`, `.claude/settings.local.json`, `.hermes/`.
- Database dumps (`*.sql`), local backups (`.backups/`), logs.

These are covered by `.gitignore`. Do not read, echo, or paste secret/private files into commits, code, or chat. If a secret is found tracked, stop and flag it.

## 4. Git workflow

- Work on a feature/chore branch; never commit directly to `main`.
- Keep changes minimal and scoped; match surrounding code style.
- Stage, commit, or push **only when the user explicitly asks**.
- Conventional, descriptive commit messages.

## 5. Scope of this setup phase

This formalization phase is **local files only**. It does **not** deploy, push, publish, or change any live WordPress state. Do not stage/commit/push, run deploys, or mutate the production site as part of repo setup unless separately and explicitly instructed.
