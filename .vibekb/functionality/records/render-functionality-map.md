---
id: render-functionality-map
type: functionality
title: Render the interactive functionality map
area: dynamic-guide
summary: The overview's first screen is an interactive, zoomable map of the application's functionality — Level 1 areas expand to Level 2 capabilities and open into the Level 3 documentation — built from the existing model with no parallel data, degrading to an accessible list without JavaScript.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A reader opens the overview (the guide's landing page) in either output mode.
updated: 2026-07-22
tags: [guide, map, overview, navigation, mode-a, mode-b, progressive-enhancement]
files: [guide/lib/map.php, guide/templates/partials/functionality-map.php, guide/templates/overview.php, guide/assets/js/guide.js, guide/assets/css/guide.css]
reads: [.vibekb/functionality, .vibekb/system, .vibekb/files/important-files.json, .vibekb/diagrams, .vibekb/work/current.md]
writes: []
config: []
depends_on: [load-living-model, resolve-relationships, render-guide]
related_memory: [decision:functionality-first-not-files, constraint:no-build-step-portable]
---

## In one sentence

Before any documentation, the guide shows a high-level map of what the software
does — areas → capabilities → docs — so a reader builds a mental model in
seconds, then clicks into the pages the rest of the guide already renders.

## Current behavior

`build_functionality_map()` (`guide/lib/map.php`) turns the living model into the
map's data — inventing nothing. Level 1 nodes are the functionality groups
(`functionalityGroups()`); Level 2 nodes are the functionality records inside an
area; every "open documentation" link is the same detail page
(`functionality_url()`) the rest of the guide uses. It also assembles live,
clickable statistics (functionalities, areas, systems, key files, relationships,
diagrams) and a current-context signal read from `work/current.md`'s
`affected_functionality` — architected so `php tools/vibekb.php context` can feed
it later without changing the shape.

The partial `partials/functionality-map.php` renders three things from that one
model: a slim hero, the statistics, and — always in the DOM — an accessible
fallback of expandable area cards that link straight into the docs. On capable
screens `guide.js` builds an interactive, pannable/zoomable canvas on top of the
fallback (Level 1 areas radiating from the application node; clicking an area
lazily expands its Level 2 children; double-click opens documentation; a current
context glows). Without JavaScript, on small screens, or in list mode, the
fallback is the experience. Both are the same data, so they cannot disagree, and
both output modes share the builder so the map never diverges from the guide.

## Step-by-step flow

1. `overview.php` requires the map partial as the page's centerpiece.
2. The partial calls `build_functionality_map()` and renders stats + fallback,
   and embeds the model as JSON for the client.
3. `guide.js` `initFunctionalityMap()` reads the JSON; on `min-width: 760px` it
   builds the canvas, otherwise the fallback cards remain the experience.
4. Expansion is lazy — Level 2 nodes exist only for expanded areas.

## Implementation map

- `guide/lib/map.php` — the map model builder (areas, children, stats, context).
- `guide/templates/partials/functionality-map.php` — hero, stats, canvas mount,
  accessible fallback, embedded JSON.
- `guide/templates/overview.php` — includes the partial as the first screen.
- `guide/assets/js/guide.js` — `initFunctionalityMap()` (pan/zoom/expand/tooltip,
  list-mode toggle, mobile handling).
- `guide/assets/css/guide.css` — the map's styles (`.fmap*`).

## Data used

- **Reads:** functionality groups and records, system doc count, important-files,
  diagrams, and the current work record (for context highlighting).
- **Writes:** nothing.

## Failure cases

- No JavaScript, small screen, or list mode → the accessible fallback renders the
  same areas, capabilities, and documentation links.
- A malformed embedded model → the script returns early, leaving the fallback.

## Safe to change

Node styling, layout radii, copy, and which statistics are shown are safe. The
fallback markup is the accessibility contract — keep it a real, linked list.

## Use caution

The map must never become a new source of truth: it is a visualization of the
existing model. Keep it reading through `Content` accessors, keep every link
pointing at the real documentation, and keep the no-JavaScript fallback working.

## Why it works this way

VibeKB's promise is "understand what your software is doing." Leading with a map
answers that in seconds and makes the diagram the primary navigation, while the
written pages remain the detail — understanding first, documentation second.
