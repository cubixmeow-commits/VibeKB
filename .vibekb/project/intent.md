---
id: intent
type: project
title: Why VibeKB exists
summary: AI builds and changes software faster than a person can maintain an accurate mental model of it; VibeKB keeps that model accurate, honest about verification, and usable at every point in the project's life.
updated: 2026-07-22
---

## The problem

People build software with coding agents (Claude Code, Cursor, Codex, Copilot,
Gemini CLI). The agent can change six files faster than the human can rebuild
their mental model. The result is a working app the owner no longer fully
understands: "I know it works, but I don't know how," "the AI says it's done but
I can't verify it," "I don't know which files matter."

## The promise

> **Understand what your software is doing.**

Everything in VibeKB exists to support that outcome. Repository memory
(decisions, constraints, warnings, discoveries, changes) exists only because it
keeps the explanation of functionality **accurate and resistant to drift** — not
as an archive in its own right.

## What VibeKB must never become

- A documentation generator that describes *intended* behaviour as if it were
  implemented.
- A tool that claims something is verified because an AI edited it or a file
  exists.
- A code browser or an activity log.
- A system that pretends it auto-updates. VibeKB is **agent-maintained**: it can
  detect mechanically that code changed, but interpreting that change into the
  model requires an agent or analysis step, and it says so.

## The two product tests

Before adding any page, record, or feature:

1. Does this help a developer understand what the software is doing right now?
2. Does this help keep that explanation accurate as the software changes?

If neither is true, it does not belong.
