---
id: resolve-relationships
type: functionality
title: Resolve relationships between records
area: living-model
summary: The loader turns the flat set of records into a graph — functionality dependencies and derived dependents, memory back-links, file-to-functionality links, and diagram cross-links — so every view can show live, validated connections.
status: implemented
verification: verified-from-source
user_facing: false
trigger: Any view or tool asks the loader for a record's related functionality, files, memory, dependents, or diagrams.
updated: 2026-07-22
tags: [relationships, graph, backlinks]
files: [guide/lib/Content.php]
reads: []
writes: []
depends_on: [load-living-model]
related_memory: [decision:functionality-first-not-files]
---

## In one sentence

Records declare *forward* links (`depends_on`, `related_memory`, `functionality`,
`files`); the loader resolves them and derives the *reverse* links (dependents,
"memory for this functionality", "files for this functionality") so authors only
state a relationship once.

## Current behavior

- `resolveFunctionality()` / `resolveMemory()` / `resolveDiagrams()` turn id
  references into linkable summaries, flagging any id that does not resolve so
  templates can render a broken-chip warning.
- `dependentsOf()` computes reverse dependencies from every record's
  `depends_on`.
- `filesForFunctionality()`, `memoryForFunctionality()`,
  `diagramsForFunctionality()`, and `diagramNodesForFunctionality()` gather the
  records and diagram nodes that point back at a functionality id.

## Implementation map

- `guide/lib/Content.php` — all `resolve*`, `*For*`, and `dependentsOf` methods.

## Failure cases

An unresolved reference is never fatal: it is returned with `resolved => false`
and surfaced as a validation issue and a ⚠ chip.

## Safe to change

Adding a new resolved relationship is safe as long as unresolved ids stay
non-fatal and are reported.

## Why it works this way

Deriving reverse links means the model can never contradict itself about, say,
which functionality a warning affects — there is exactly one place the link is
written.
