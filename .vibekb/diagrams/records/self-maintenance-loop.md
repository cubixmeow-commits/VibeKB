---
id: self-maintenance-loop
type: diagram
title: The self-maintenance lifecycle
summary: The loop an agent follows so the model stays synchronized with the code — from `vibekb status` at session start to an honest handoff.
diagram_type: user-journey
group: workflow
svg: self-maintenance-loop.svg
functionality: [start-work-session, record-current-work, find-affected-functionality, detect-drift, hand-off-to-next-agent]
files: [tools/vibekb.php, CLAUDE.md]
data: []
warnings: [model-can-drift-from-code]
diagrams: [vibekb-architecture]
status: implemented
verification: verified-manually
provenance: The steps correspond to the CLAUDE.md lifecycle and the vibekb.php subcommands; the loop was exercised while building this change (dogfooding). This diagram is a picture + narrative and deliberately has no explainable topology — its edges are process transitions, not code mechanisms.
last_verified: 2026-07-22
uncertainty: This is a process diagram, not a code graph; it is intentionally not an explainable topology.
created: 2026-07-22
updated: 2026-07-22
---

## What am I looking at?

The maintenance loop, left to right. A session **starts** with
`php tools/vibekb.php status`. The agent **understands** the affected
functionality and risks, **records** the work in `current.md`, **implements** the
code change, uses **`affected`** to find impacted records, **updates the model**
(records, memory, diagrams, provenance), runs **`check`** (validate + drift +
snapshot sync), **generates** `/docs`, and writes the **handoff** the next session
reads.

## Why it matters

It makes the product's core claim concrete: VibeKB is used at the beginning,
middle, and end of a coding session — not bolted on afterwards. The two CLI touch
points (`status`, `check`/`affected`) are what keep the loop low-friction.

## What is uncertain

This is a **process** diagram: its arrows are workflow transitions, not code
mechanisms, so it deliberately has no explainable topology (the explainability
gate is for relationships you can name a concrete code mechanism for). The
underlying steps are real and defined in `CLAUDE.md` and `tools/vibekb.php`.
