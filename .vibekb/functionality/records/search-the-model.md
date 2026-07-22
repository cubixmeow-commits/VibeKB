---
id: search-the-model
type: functionality
title: Search the model
area: dynamic-guide
summary: A shared index builder produces one search index covering functionality, memory, files, diagrams, and explainable node/edge/file entries; the dynamic guide serves it as JSON and the static snapshot ships it as a file, with client-side search that needs no server, database, or CDN.
status: implemented
verification: verified-from-source
user_facing: true
trigger: The Search view loads; the dynamic guide also exposes ?view=search&data=json.
updated: 2026-07-22
tags: [search, client-side, index]
files: [guide/lib/search.php, guide/templates/search.php, guide/assets/js/guide.js]
reads: [.vibekb]
writes: []
depends_on: [load-living-model, render-explainable-diagrams]
related_memory: [constraint:no-build-step-portable, decision:two-modes-one-source]
---

## In one sentence

`build_search_index()` walks the loaded model once to produce link-resolved
entries — including deep links to individual diagram nodes and edges — that a
small vanilla-JS search filters entirely in the browser.

## Current behavior

The same index builder is used by both modes: the dynamic guide serves it live at
`?view=search&data=json`, and the generator writes it to
`docs/assets/data/search.json` with links built for the static location. The
Search view and `guide/assets/js/guide.js` filter it client-side; there is no
jQuery and no required CDN.

## Implementation map

- `guide/lib/search.php` — `build_search_index()` (shared by both modes).
- `guide/templates/search.php` — the search UI.
- `guide/assets/js/guide.js` — client-side filtering.

## Failure cases

- With JavaScript off, the Search view degrades to a browsable list; nothing
  essential is lost because every entry is a real link.

## Why it works this way

One index builder means the static search cannot list pages the dynamic guide
would not, and the validator checks that every static search entry points at a
page that exists.
