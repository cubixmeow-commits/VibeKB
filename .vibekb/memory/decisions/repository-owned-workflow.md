---
id: repository-owned-workflow
type: decision
title: The maintenance workflow is repository-owned, not agent-specific
summary: The canonical lifecycle lives in the repository (CLAUDE.md + the vibekb CLI); agent-specific entry points (AGENTS.md, .cursor/rules) are thin pointers, so the workflow cannot fork per tool.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [start-work-session, hand-off-to-next-agent]
files: [CLAUDE.md, AGENTS.md, tools/vibekb.php]
tags: [workflow, interoperability, agents]
---

## Context

VibeKB must work with Claude Code, Cursor, Codex, and others. If each agent gets
its own full copy of the instructions, the copies drift and the "workflow"
becomes whichever file the current agent happened to read.

## Decision

There is one canonical, repository-owned operating document (`CLAUDE.md`) and one
canonical toolchain entry point (`php tools/vibekb.php`). Agent-specific files —
`AGENTS.md`, `.cursor/rules/vibekb.mdc` — are short and point at the canonical
source rather than duplicating it. The lifecycle is executable (the CLI), not
just prose.

## Alternatives considered

- **A large per-agent instruction file each** — rejected: guaranteed drift and
  duplicated maintenance.
- **Prose-only workflow, no tooling** — rejected: prose alone gets skipped; the
  CLI makes the lifecycle low-friction and discoverable.

## Reason

A new agent should discover the workflow from the repository and follow it
without the user pasting a giant prompt every session.

## Consequences

- Changing the workflow means changing one document and one CLI.
- Any agent that reads `AGENTS.md` or the Cursor rule is routed to the same
  canonical lifecycle and the same `status`/`check` commands.
