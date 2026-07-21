---
id: add-diagrams-and-provenance
type: change
title: Added a source-grounded diagram set and provenance to the SousMeow model
summary: The SousMeow model gained five source-grounded diagrams and an explicit provenance block; no functionality claim was upgraded.
functionality: [run-recipe, access-database, route-and-secure-requests]
files: [.vibekb/diagrams/index.json, .vibekb/manifest.json]
created: 2026-07-21
updated: 2026-07-21
---

## Before

The SousMeow model had no diagrams and no explicit provenance block. The
overview relied on an undefined "Last meaningful update" label and counted
functionality records as "areas".

## After

- Added `.vibekb/diagrams/` with five diagrams: app overview, the Run-a-Cookbook
  loop, request flow, storage map, and a risk & uncertainty map. Each has an
  accessible SVG and links to related functionality and warnings.
- Added a `provenance` block to `manifest.json` (source commit analysed,
  verification scope, last verified, `updates_automatically: false`).
- Counts now read "N functionality records across M functional areas".

## Impact

Documentation only — SousMeow itself was not touched and no verification state
was upgraded for presentation. The diagrams reflect existing verified records;
inferred areas (request-flow's Router step, `manage-account`,
`seed-and-sync-content`, bulk `demo-simulation`) are labelled as such in the
diagrams. This change is about the VibeKB *model of* SousMeow, produced while
integrating reusable lessons from the StopPR field test.
