---
id: start-work-session
type: functionality
title: Start a work session
area: agent-workflow
summary: `php tools/vibekb.php status` prints the one screen an agent needs to begin — the active model's provenance, the current work record, the handoff's next recommended action, and a one-line validation and drift summary — so a fresh agent can orient without reading every file.
status: implemented
verification: verified-manually
user_facing: true
trigger: A coding agent runs `php tools/vibekb.php status` (or `php tools/vibekb.php`) at the start of a session.
updated: 2026-07-22
tags: [cli, session, onboarding, self-maintenance]
files: [tools/vibekb.php]
reads: [.vibekb/manifest.json, .vibekb/work/current.md, .vibekb/work/handoff.md]
writes: []
depends_on: [load-living-model, validate-model, detect-drift]
related_memory: [decision:repository-owned-workflow, assumption:agents-follow-session-workflow]
---

## In one sentence

One command answers "where am I, what is in flight, what should I do next, and is
the model still trustworthy?" — the session-start entry point of the VibeKB
lifecycle.

## User experience

An agent (or human) runs `php tools/vibekb.php status`. It prints: the source
commit the model was reconciled against and when it was last verified; the active
`work/current.md` objective and status; the handoff summary and its exact next
recommended action; the validation error/warning counts; and a one-line drift
verdict (in sync / N files changed since the recorded commit). It never modifies
anything.

## Step-by-step flow

1. Load the model through `Content` (the same loader the guide uses).
2. Read provenance from the manifest.
3. Read the `current.md` and `handoff.md` front matter and summaries.
4. Run the lightweight drift summary (see **Detect drift**).
5. Print the report; exit 0 (status is read-only and never fails the session).

## Implementation map

- `tools/vibekb.php` — the `status` subcommand (default when no subcommand given).

## Failure cases

- No current work / handoff → the report says so plainly rather than erroring.

## Why it works this way

The lifecycle only works if orientation is one cheap command, not a required
reading list. Making the next action visible on session start is what stops an
agent from treating VibeKB as an afterthought at the end of a change.
