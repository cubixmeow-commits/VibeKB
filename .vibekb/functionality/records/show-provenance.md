---
id: show-provenance
type: functionality
title: Show provenance and freshness
area: dynamic-guide
summary: Every rendering carries an objective provenance panel — source commit analyzed, analysis generated, verification scope, last verified, and an explicit "does not auto-update" — built once and reused by both output modes so no page can imply freshness it does not have.
status: implemented
verification: verified-from-source
user_facing: true
trigger: Any page renders the provenance panel/stamp; the generator supplies the static generation context.
updated: 2026-07-22
tags: [provenance, honesty, freshness]
files: [guide/lib/Provenance.php, guide/templates/overview.php, guide/templates/layout.php]
reads: [.vibekb/manifest.json]
writes: []
depends_on: [load-living-model]
related_memory: [decision:honest-provenance-no-auto-update, warning:verification-must-reflect-evidence]
---

## In one sentence

Provenance separates two facts that must never be conflated — the *source* the
model explains (repo, commit, verification) and the *generation* event that
produced this output (mode, time, generator commit) — and states plainly that
neither implies auto-update.

## Current behavior

`provenance_data()` normalises the manifest `provenance` block plus a render-time
generation context (dynamic vs static). `provenance_panel()` renders objective
labelled rows; `provenance_stamp()` renders the compact footer notice. Missing
values are omitted, never invented. `updates_automatically` renders as "No —
regenerate to refresh" unless a real update mechanism sets it true.

## Implementation map

- `guide/lib/Provenance.php` — normalisation, panel, stamp, disclaimer.
- `guide/templates/overview.php` — the Overview provenance panel.
- `guide/templates/layout.php` — the footer stamp.

## Use caution

Do not add a provenance field that overstates freshness. The generation
timestamp is the only per-render volatile value; keep it out of any equality that
should be commit-stable (the drift check normalises it).

## Why it works this way

VibeKB's credibility is provenance. A single component, reused by both modes,
means the dynamic guide and the static snapshot make the same honest claims.
