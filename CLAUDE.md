# CLAUDE.md — Wicked Evolutions Theme (Claude Code adapter)

The canonical operating contract for this repository is **[AGENTS.md](AGENTS.md)** —
read it first and follow it (what this repo is, the WordPress-native authority
order, Abilities MCP first, public-repo security, no deploy/live mutation, and
working discipline). This file adds only Claude Code-specific collaboration
mechanics; it does not restate or override the contract.

## Claude-specific mechanics

- **Print-mode heavy worker.** Claude Code is the primary heavy-lifting agent here and may run non-interactively (print / `-p` mode) for batch edits, multi-file changes, and tool-driven work. Apply the same AGENTS.md discipline whether interactive or in print mode.
- **No stage/commit/push/deploy unless explicitly instructed.** Make file edits freely on a feature/chore branch, but do not `git add`/`commit`/`push` or run any deploy/publish command unless the user explicitly asks for that specific action.
- **Preserve the IJFW-MEMORY managed block.** The `<!-- IJFW-MEMORY-… -->` block below is generated and managed by IJFW — keep it intact and do not hand-edit its contents.

<!-- IJFW-MEMORY-START (managed -- do not edit manually) -->
<ijfw-memory>
Project memory at .ijfw/memory/. Call `ijfw_memory_prelude` for full context.
</ijfw-memory>
<!-- IJFW-MEMORY-END -->
