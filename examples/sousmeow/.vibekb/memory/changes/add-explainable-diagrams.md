---
id: add-explainable-diagrams
type: change
title: Made diagrams explainable — nodes, edges, mechanisms, files-with-reasons
summary: Two SousMeow diagrams gained a repository-owned topology so every node states what it is, every edge states a concrete mechanism, every file states why it matters, and external source links reach the implementation — without upgrading any verification claim.
functionality: [route-and-secure-requests, run-recipe, access-database]
files: [.vibekb/diagrams/topology/request-flow.json, .vibekb/diagrams/topology/run-recipe-flow.json, .vibekb/diagrams/assets/request-flow.svg, .vibekb/diagrams/assets/run-recipe-flow.svg]
created: 2026-07-21
updated: 2026-07-21
---

## Before

Diagrams were a picture plus prose. There was no way to select a box or arrow
and learn what it is, why it connects to its neighbour, which files implement
it, or where to open the code. Relationships had no stated mechanism.

## After

- Added an optional per-diagram topology (`.vibekb/diagrams/topology/<id>.json`)
  with nodes (title + plain-language purpose), edges (a controlled **mechanism**
  and a one-sentence explanation), per-node/edge files with roles and reasons,
  repository locations, and honest verified/inferred states.
- Upgraded two representative diagrams: **Request flow** (verified web path with
  two inferred, dashed edges — the Router match and the CLI write path) and
  **Run a Cookbook — the core loop** (a verified user journey with the pasted-
  response and read/write-coupling warnings attached).
- Marked each SVG's node/edge groups with `data-vibekb-node` /
  `data-vibekb-edge`, linked to `#node-<id>` / `#edge-<id>` anchors so every
  explanation is reachable without JavaScript. External source links are
  commit-pinned to the analysed SousMeow commit; no line numbers were invented.

## Impact

VibeKB product + model only — SousMeow itself was not touched, and no
verification state was upgraded for presentation. The topology reuses existing
functionality records, `important-files.json`, and warnings rather than
duplicating them. Inferred edges are drawn dashed and state their basis;
verified edges are solid. The loader, `tools/validate.php`, and a new
`tools/test-topology.php` enforce the topology contract (unique ids, resolvable
edges, controlled mechanisms, files-with-reasons, SVG↔topology marker mapping).
The other three diagrams remain valid as picture + narrative.
