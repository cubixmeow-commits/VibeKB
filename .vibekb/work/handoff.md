---
id: handoff
type: handoff
title: Current handoff
summary: The overview's first screen is now an interactive functionality map (areas → capabilities → docs) built from the existing model; documentation is second, not first. Next: keep the model reconciled as VibeKB changes.
updated: 2026-07-22
verification_state: verified-manually
---

## Completed this change

- New `guide/lib/map.php` — `build_functionality_map()` derives the map (Level 1
  areas, Level 2 capabilities, live statistics, current-context signal) from the
  existing `Content` model. No parallel data structures.
- New `guide/templates/partials/functionality-map.php` — hero + live statistics +
  interactive canvas mount + an always-present accessible fallback (expandable
  area cards linking straight into the docs) + embedded JSON model.
- `guide/templates/overview.php` restructured so the map is the centerpiece; the
  written sections (what it does, how to think, warnings/work, next step) remain
  below as progressive Level-3 detail. Removed the old page-head/snapshot-bar and
  the redundant "functional areas" list (the map now covers Level 1/2 nav).
- `guide/assets/js/guide.js` — `initFunctionalityMap()`: pan/zoom (wheel, drag,
  pinch), lazy expand/collapse, hover tooltip, current-context glow/dim,
  list-mode toggle, and a mobile path that keeps the accessible cards.
- `guide/assets/css/guide.css` — the `.fmap*` styles (premium, theme-consistent).
- `guide/lib/Content.php` — added `systemDocs()` so the "Systems" statistic is
  counted, not hard-coded.
- Model reconciled: new `render-functionality-map` functionality record (+ index
  order), `render-guide` updated, two new important-files entries, manifest
  provenance updated (source_commit + verification scope), `/docs` regenerated.
- `guide/index.php` and `tools/generate-static.php` now require `lib/map.php`.

## Verification completed

- `php -l` on every changed PHP file; `node --check guide/assets/js/guide.js`.
- Rendered the overview in Mode A (PHP built-in server) and Mode B (static): the
  embedded JSON parses (7 areas, 6 stats), area/capability links resolve, and the
  static links are relative/subpath-safe.
- Headless Chromium: desktop canvas builds (app + 7 area nodes), single-click
  expands an area's capabilities, double-click opens the capability's doc page,
  the List control swaps to the fallback and back, and at 390px width the canvas
  is hidden and the accessible cards are the experience.
- `php tools/vibekb.php check` — 0 errors (1 pre-existing warning:
  self-maintenance-loop has no topology yet); no broken references; `/docs` in
  sync after `generate`. `php tools/test-topology.php` OK.

## Current-context feature

The map highlights the functionality an agent is actively working on. It reads
`work/current.md`'s `affected_functionality` today and is architected so
`php tools/vibekb.php context` can feed the same shape later with no template or
JS change.

## Exact next recommended action

`php tools/vibekb.php status` before the next change; `affected` → update model →
`check` + `generate` before commit.
