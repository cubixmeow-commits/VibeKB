---
id: risk-uncertainty-map
type: diagram
title: Risk and uncertainty map
summary: A single view of the active warnings and the still-inferred areas of the SousMeow model, so an agent sees the landmines before changing code.
diagram_type: risk-and-uncertainty-map
group: uncertainty
svg: risk-uncertainty-map.svg
functionality: [manage-account, seed-and-sync-content, demo-simulation, reset-password]
files: []
data: []
warnings: [pasted-response-is-untrusted, read-write-path-coupling, password-reset-depends-on-smtp, legacy-category-column]
diagrams: [storage-map, run-recipe-flow]
status: implemented
verification: verified-from-source
provenance: Assembled from the recorded warnings and the honestly inferred functionality states in this model. Source evidence — .vibekb/memory/warnings/, .vibekb/work/handoff.md.
last_verified: 2026-07-16
uncertainty: This is a living map — it is only as current as the warnings and verification states it summarises. Update it whenever those change.
created: 2026-07-21
updated: 2026-07-21
---

## What am I looking at?

Where to be careful in the SousMeow model, in one place:

- **Active warnings** (red = defect risk, amber = configuration/deployment
  risk): pasted responses are untrusted and must stay escaped; Pantry and
  Artifact read/write paths are coupled; password reset silently no-ops without
  SMTP; the legacy category column must never be read.
- **Inferred from source** (grey, dashed): `manage-account`,
  `seed-and-sync-content`, and bulk `demo-simulation` are not line-traced yet —
  trace before trusting.

## Why it matters

An agent should read this before editing SousMeow behaviour. It converts the
handoff's caveats and the warning records into a single visual checklist.

## What is uncertain

By construction this diagram *is* the uncertainty view. It is only as accurate
as the warnings and verification states it summarises; regenerate it when those
change.
