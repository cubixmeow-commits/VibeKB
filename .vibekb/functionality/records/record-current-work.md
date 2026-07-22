---
id: record-current-work
type: functionality
title: Record current AI work
area: agent-workflow
summary: `.vibekb/work/current.md` holds the active change — requested outcome, current vs proposed behaviour, affected functionality, expected files, data impact, risks, and verification plan — rendered as the Current Work view so the change is visible before, during, and after it happens.
status: implemented
verification: verified-from-source
user_facing: true
trigger: An agent updates work/current.md before implementing; the guide renders it at ?view=current-work.
updated: 2026-07-22
tags: [work, coordination, transparency]
files: [.vibekb/work/current.md, guide/templates/current-work.php]
reads: [.vibekb/work/current.md]
writes: []
depends_on: [load-living-model]
related_memory: [assumption:agents-follow-session-workflow]
---

## In one sentence

The active-work record is the coordination artifact that makes an in-flight
change legible — what is being attempted, what it touches, and how it will be
verified — not a log written after the fact.

## Current behavior

`current.md` front matter carries `objective`, `status`, `verification_state`,
`affected_functionality`, `expected_files`, `data_impact`, and `risks`; the body
carries the narrative (what was asked, current behaviour, proposed behaviour,
verification plan). `Content` loads it and `current-work.php` renders it as the
Current Work view in both output modes.

## Implementation map

- `guide/templates/current-work.php` — the view.
- `guide/lib/Content.php` — `currentWork()`.

## Safe to change

The body prose and which fields are emphasised are safe to adjust.

## Use caution

Keep `status`/`verification_state` honest. Marking work `completed` here does not
make it verified — that is what the verification state is for.

## Why it works this way

Recording the change *before* implementing is step 2 of the lifecycle; it is what
lets the handoff and the drift check reason about whether the model reflects the
current task.
