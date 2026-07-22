---
id: mental-model
type: system
title: Mental model
summary: One repository-owned model, one loader, one template set, two output modes, and an agent-run maintenance lifecycle that keeps the model true.
updated: 2026-07-22
---

## The one-paragraph model

VibeKB is a **model** (`.vibekb/`), a **renderer** (`guide/`), a set of
**tools** (`tools/`), and a **workflow** (the agent lifecycle). The model is the
source of truth. The loader (`guide/lib/Content.php`) reads it into memory and
validates it. The renderer turns it into pages through one template set, in two
modes — the live PHP guide (Mode A) and the static `/docs` snapshot (Mode B) —
so the two can never disagree. The tools validate the model, generate the
snapshot, and (new) help an agent run the lifecycle and detect drift. The
workflow is what keeps the model synchronized with the code as agents change it.

## What is the primary unit

**Functionality** — the things the software does — not files, decisions, or
sessions. Files, memory, and diagrams all link back to functionality.

## The honesty invariants

- Intended, implemented, and verified are different; every record states which.
- Nothing claims to auto-update; `updates_automatically` is `false`.
- Detection (mechanical: git diff, path existence, render diff) is never
  presented as interpretation (an agent deciding what a change means).

## How to read this repository

Start at the Overview, then Functionality. `guide/` is the renderer, `tools/` is
the tooling, `.vibekb/` is the content, `examples/` is demonstration material,
`/docs` is generated output.
