---
id: render-explainable-diagrams
type: functionality
title: Render explainable diagrams
area: diagrams
summary: A diagram can carry a repository-owned topology (nodes with purposes, edges with controlled mechanisms, files with reasons, commit-pinned source links); the loader resolves it and the guide renders semantic per-node/per-edge explanations that work without JavaScript, identical in both output modes.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A diagram record declares a `topology:` file; the Diagrams view renders it.
updated: 2026-07-22
tags: [diagrams, explainable, topology, no-js]
files: [guide/lib/Content.php, guide/templates/diagrams.php, guide/templates/partials/diagram-explain.php, guide/assets/js/guide.js, guide/lib/helpers.php]
reads: [.vibekb/diagrams]
writes: []
depends_on: [load-living-model, resolve-relationships]
related_memory: [decision:functionality-first-not-files]
---

## In one sentence

An explainable diagram is a visual projection of the model: every node states
what it is, every edge states the concrete mechanism connecting its endpoints,
and every displayed file states why it matters, with an external source link as
the terminal "show me the implementation."

## Current behavior

`Content` loads a diagram's topology JSON, normalises it, resolves each
node/edge's functionality and warnings, enriches files with canonical metadata
from `important-files.json`, and builds commit-pinned GitHub source links from
provenance. `diagrams.php` and the `diagram-explain` partial render semantic
sections with stable `#node-<id>` / `#edge-<id>` anchors; the SVG marks each group
with `data-vibekb-node` / `data-vibekb-edge` linking to those anchors, so a
no-JavaScript reader can follow any element to its explanation. A small vanilla-JS
enhancement adds selection and dimming. Verified edges render solid, inferred
edges dashed — and the state is always also stated in text.

## Implementation map

- `guide/lib/Content.php` — `loadTopology()`, `resolvedTopology()`, file metadata
  reuse, source links.
- `guide/templates/diagrams.php` + `partials/diagram-explain.php` — rendering.
- `guide/lib/helpers.php` — the controlled edge-mechanism and file-role
  vocabularies and source-link builder.

## Use caution

Never draw an edge without a stateable mechanism from the controlled vocabulary;
never mark an inferred edge verified. A missing edge is better than a false one.

## Why it works this way

Diagrams teach how the software works before any click, and every click answers
the reader's next question — without becoming a code browser or an IDE.
