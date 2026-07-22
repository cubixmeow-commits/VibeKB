---
id: vibekb-architecture
type: diagram
title: VibeKB architecture — one source, one loader, two modes
summary: How the repository-owned model, the loader, the shared templates, the two output modes, and the self-maintenance CLI fit together.
diagram_type: application-overview
group: whole-app
svg: vibekb-architecture.svg
topology: vibekb-architecture.json
functionality: [load-living-model, render-guide, generate-static-snapshot, validate-model, detect-drift]
files: [guide/lib/Content.php, guide/index.php, tools/generate-static.php, tools/vibekb.php]
data: []
warnings: []
diagrams: [content-load-flow]
status: implemented
verification: verified-from-source
provenance: Traced from source — guide/lib/Content.php (loader), guide/index.php (Mode A), tools/generate-static.php (Mode B), tools/vibekb.php (CLI). All edges are verified-from-source.
last_verified: 2026-07-22
uncertainty: None material — every node and edge maps to code in this repository.
created: 2026-07-22
updated: 2026-07-22
---

## What am I looking at?

The whole system on one screen. The **content model** (`.vibekb/`) is the single
source of truth. One **loader** reads it. Both output modes — the **dynamic
guide** (Mode A) and the **static generator** (Mode B) — use that loader and the
same **templates**, so they cannot disagree. The **maintenance CLI** validates the
model and drives the lifecycle.

## Why it matters

It shows the load-bearing invariant: there is one source and one template set.
Anything that would give a mode its own renderer or its own model breaks the
product's honesty guarantee that the public site is the live guide, frozen.

## What is uncertain

Nothing material — every node and edge is traced to code in this repository, so
all edges render solid (verified).
