# Abilities Gap Log

## Purpose

Track operations that **could not** be completed through the WordPress Abilities MCP (`mcp__wordpress__*`), or that required a non-ability workaround. Per the Abilities-first contract in [`CLAUDE.md`](../../CLAUDE.md), every such gap is recorded here. Gaps are the product roadmap — this log is the input to closing them.

## Rules

- Log a gap when an Abilities call **fails**, an ability **does not exist** for a needed operation, or a workaround (SSH, raw REST, manual UI) was required.
- One row per distinct gap. If the same gap recurs, update its row rather than adding duplicates.
- Record only **observed** events. Do not invent or speculate failed calls.
- When a gap is closed (ability added/fixed), set **Status** to `Resolved` and note how.

## Log

| Date | Operation attempted | Site key | Ability/tool attempted | Result | Gap | Fallback used | Status |
|------|---------------------|----------|------------------------|--------|-----|---------------|--------|
| | | | | | | | |

<!--
Row template:
| 2026-06-02 | Short description of the operation | site key (e.g. wickedevolutions.com) | ability name or tool | What happened | Why it could not be done via Abilities | What was done instead (or "none") | Open / Resolved |
-->
