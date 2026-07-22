---
id: hand-off-to-next-agent
type: functionality
title: Hand off to the next agent
area: agent-workflow
summary: `.vibekb/work/handoff.md` records current functionality state, completed work, verification done, unresolved work, active warnings, and the exact next recommended action — the artifact a fresh agent reads (via `status`) to continue without re-deriving context.
status: implemented
verification: verified-from-source
user_facing: true
trigger: An agent updates work/handoff.md at the end of a session; the guide renders it at ?view=handoff and `status` surfaces its next action.
updated: 2026-07-22
tags: [handoff, coordination, session]
files: [.vibekb/work/handoff.md, guide/templates/handoff.php]
reads: [.vibekb/work/handoff.md]
writes: []
depends_on: [load-living-model, start-work-session]
related_memory: [assumption:agents-follow-session-workflow, decision:repository-owned-workflow]
---

## In one sentence

The handoff is the session's terminal state: what is true now, what was verified,
what remains, and the single most useful next action — written for whoever
(human or AI) arrives next.

## Current behavior

`handoff.md` carries a `verification_state` and a summary in front matter and a
structured body (what the software does now, completed work, verification,
unresolved work, active warnings, exact next recommended action). `Content`
exposes it and `handoff.php` renders it; the `status` command reads its summary
and next action so orientation does not require opening the file.

## Implementation map

- `guide/templates/handoff.php` — the view.
- `tools/vibekb.php` — `status` surfaces the summary and next action.

## Use caution

The handoff must reflect the repository state at hand-off time. A stale handoff
is worse than none, because `status` presents it as current. Update it as the
last step of every change.

## Why it works this way

A living model only stays continuous across sessions if each session ends with an
honest, actionable handoff. It is step 7 — and the thing `status` reads first.
