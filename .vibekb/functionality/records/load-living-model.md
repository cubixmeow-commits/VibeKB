---
id: load-living-model
type: functionality
title: Load the living model
area: living-model
summary: A single loader reads the repository-owned `.vibekb/` directory into an in-memory model — project, functionality, system, files, diagrams, memory, and work — with all filesystem access confined to the content root.
status: implemented
verification: verified-from-source
user_facing: false
trigger: The guide (guide/index.php) or a tool constructs `Content` with a content root and calls `load()`.
updated: 2026-07-22
tags: [loader, content-model, core]
files: [guide/lib/Content.php, guide/lib/FrontMatter.php, guide/lib/Markdown.php]
reads: [.vibekb/manifest.json, .vibekb/project, .vibekb/functionality, .vibekb/system, .vibekb/files/important-files.json, .vibekb/diagrams, .vibekb/memory, .vibekb/work]
writes: []
config: [VIBEKB_CONTENT_ROOT]
depends_on: [parse-records]
related_memory: [decision:functionality-first-not-files, constraint:confine-file-access-to-content-root]
---

## In one sentence

`Content::load()` turns a directory of Markdown-plus-front-matter records and
small JSON manifests into a single, queryable software model — no database, no
network, no build step.

## Current behavior

`Content` is constructed with a content root (by default the repository's own
`.vibekb/`; an explicit `VIBEKB_CONTENT_ROOT` pointing at a `.vibekb` directory
lets the same code preview a bundled example). `load()` reads, in order: the
manifest and its provenance; the four project documents; the functionality index
and every `functionality/records/*.md`; the system documents; the important-files
list; the diagrams index, records, and any explainable topology; every memory
type; and the current work, handoff, and sessions. Records are keyed by id, with
duplicate ids reported as issues rather than silently overwritten.

## Step-by-step flow

1. Read `manifest.json`; normalise its provenance for source links.
2. Load project, functionality, system, files, diagrams, memory, and work.
3. For each diagram that declares a `topology`, load and normalise the JSON graph.
4. Run `validate()` (see **Validate the model**) to populate the issues list.

## Implementation map

- `guide/lib/Content.php` — the loader, accessors, relationship resolution, and
  validation (this file is the spine of the model).
- `guide/lib/FrontMatter.php`, `guide/lib/Markdown.php` — parsing (see
  **Parse records**).

## Data used

- **Reads:** everything under the content root.
- **Writes:** nothing. The loader never mutates the repository.

## Dependencies

Parsing (`parse-records`) turns each file into `{meta, body, html}`.

## Failure cases

- A missing or malformed file is recorded as an issue and skipped, never fatal.
- A path that would escape the content root is refused by `isInsideRoot()`.

## Safe to change

Adding a new accessor or memory type is low risk if you keep reads confined to
the root and report problems as issues instead of throwing.

## Use caution

Every new file read must go through the root-confinement check. Do not introduce
a read that trusts a caller-supplied path.

## Why it works this way

One loader, used by the guide and every tool, means the dynamic guide, the
static snapshot, and the validator can never disagree about what the model says.
