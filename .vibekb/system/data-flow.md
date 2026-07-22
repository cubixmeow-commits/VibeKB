---
id: data-flow
type: system
title: Data flow
summary: Content flows one way — from `.vibekb/` files, through the loader's parse-resolve-validate pipeline, into the in-memory model, out to rendered pages and the search index; the maintenance lifecycle flows the other way, from code changes back into the model via an agent.
updated: 2026-07-22
---

## Read path (files → pages)

`.vibekb/*.md` and `*.json`
→ `FrontMatter`/`Markdown` parse
→ `Content` builds the keyed model and resolves relationships
→ `validate()` accumulates issues
→ templates render pages (Mode A live, Mode B to `/docs`)
→ `build_search_index()` produces the searchable index for both modes.

Nothing writes back to `.vibekb/` on this path. The loader is read-only.

## Maintenance path (code → model)

Code changes
→ `vibekb check` detects changed files, broken references, and snapshot staleness
→ `vibekb affected` maps changed files to likely functionality (via `files[]`
back-links)
→ **an agent interprets** the change and updates the affected records, memory,
diagrams, provenance, and handoff
→ `vibekb validate` / `generate` reconcile and refresh outputs.

The detection half is mechanical; the interpretation half requires an agent. That
boundary is deliberate and stated everywhere it matters.

## Where data lives

There is no runtime database. The model is files in `.vibekb/`, versioned with the
code. The static snapshot is generated files in `/docs`. Provenance metadata lives
in `manifest.json`.
