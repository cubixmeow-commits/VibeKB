---
id: agents-follow-session-workflow
type: assumption
title: Coding agents will follow the session workflow if it is discoverable and cheap
summary: We assume a capable agent reads CLAUDE.md / AGENTS.md / .cursor rules and runs `vibekb status` at session start, rather than needing the giant bootstrap prompt each time.
status: active
confidence: medium
verification: reported-by-developer
invalidated_by: An agent repeatedly editing code without reading the workflow or running the CLI.
next_check: After the next few real changes to VibeKB by different agents.
updated: 2026-07-22
functionality: [start-work-session, record-current-work, hand-off-to-next-agent, initialize-in-a-repository]
files: [CLAUDE.md, AGENTS.md]
tags: [workflow, assumption]
---

## The assumption

A new coding-agent session (Claude Code, Cursor, or similar) will discover the
workflow from the repository — the canonical `CLAUDE.md`, the thin `AGENTS.md`,
and `.cursor/rules/vibekb.mdc` — and will run `php tools/vibekb.php status` to
orient, then follow the lifecycle through to a handoff, without the user pasting a
long prompt.

## Why we believe it (partial evidence)

- The instructions are short, canonical, and point to one executable entry point.
- This change itself was produced by following the lifecycle (dogfooding).

## Why it is only an assumption

- We have not observed a fresh Cursor session follow it end to end in this
  environment; Cursor discovery is `inferred`.
- Agents vary; some may still need a nudge.

## If invalidated

Strengthen the nudges: e.g. a SessionStart hook that prints the `status` output,
or a shorter, blunter first line in each agent file. Do not respond by duplicating
the full workflow into every agent file — that reintroduces drift.
