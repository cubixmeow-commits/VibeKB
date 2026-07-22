---
id: validate-diagram-topology
type: functionality
title: Validate diagram topology
area: diagrams
summary: The loader and `tools/test-topology.php` enforce the explainability contract — unique ids, resolvable edge endpoints, controlled mechanisms, honest verification, files with reasons, and SVG markers that map to topology ids in both directions — so a diagram can never point at something it cannot explain.
status: implemented
verification: verified-by-test
user_facing: true
trigger: The loader validates each topology on load; `php tools/test-topology.php` exercises malformed and valid fixtures.
updated: 2026-07-22
tags: [diagrams, validation, topology, test]
files: [guide/lib/Content.php, tools/test-topology.php]
reads: [.vibekb/diagrams/topology]
writes: []
depends_on: [validate-model, render-explainable-diagrams]
related_memory: [warning:verification-must-reflect-evidence]
---

## In one sentence

`validateTopology()` proves each explainable diagram keeps its promises; the
topology test proves that malformed topology produces useful diagnostics instead
of crashing the guide.

## Current behavior

The loader checks node purposes, edge mechanisms (against the controlled
vocabulary), one-sentence explanations, verification states, resolvable
functionality/warning references, safe repository-relative file paths each with a
non-empty reason, and that the SVG's `data-vibekb-node`/`data-vibekb-edge` markers
map to the topology ids in both directions. `tools/test-topology.php` feeds
deliberately broken fixtures and asserts each expected diagnostic fires, then
confirms a good topology still resolves — exiting non-zero if any expectation is
unmet.

## Implementation map

- `guide/lib/Content.php` — `validateTopology()` and `loadTopology()`.
- `tools/test-topology.php` — the fixture-driven test.

## Failure cases

- A malformed topology is reported as an issue and the diagram still renders as a
  picture + narrative; it never crashes the guide.

## Why it works this way

The explainability gate only means something if it is machine-enforced. The test
is the evidence that the enforcement itself works.
