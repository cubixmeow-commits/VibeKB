---
id: export-project-kit
type: functionality
title: Export the Project Kit
area: export-kit
summary: Builds a downloadable zip of every approved Artifact — numbered Markdown files, a self-contained HTML reader, and a provenance manifest — served only to the owner.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user posts to "/projects/{id}/export" and downloads via "/exports/{id}/download".
updated: 2026-07-16
tags: [export, deliverable, flagship, write]
files: [app/Controllers/ExportController.php, app/Services/ProjectKit.php, app/Services/SafeText.php, app/Models/Export.php, app/Models/Artifact.php]
reads: [projects, recipes, artifacts, artifact_versions, pantry_fields, pantry_values, exports]
writes: [exports]
config: [exports.dir]
depends_on: [approve-and-version]
related_memory: [decision:immutable-artifact-versions, warning:pasted-response-is-untrusted]
---

## In one sentence

Once every step is approved, export a self-contained kit — Markdown files, an
offline HTML reader, and a manifest — that only you can download.

## User experience

The export page shows readiness (all steps approved) and past exports. Building
produces a zip; the download streams as an attachment. `kit.html` opens in any
browser and works offline.

## Current behavior

`ExportController::create()` requires verification and re-checks that every
Recipe has an approved Artifact (server-side gate), then `ProjectKit::build()`
writes the zip to `exports.dir` (outside the web root): one
`NN-recipe-slug.md` per approved Artifact, a `kit.html` reader (content rendered
through `SafeText`), and a `README.md` manifest listing files and the Pantry.
`Export::record()` logs it. `download()` re-scopes the export to the owner
(`Export::findForUser`), re-anchors the filename to `exports.dir`, and streams
it with `Content-Disposition: attachment` and `X-Content-Type-Options: nosniff`.

## Step-by-step flow

1. User posts to `/projects/{id}/export`.
2. The all-approved gate is re-checked (else a notice).
3. `ProjectKit::build()` writes the zip (md files + kit.html + README.md).
4. `Export::record()` stores filename, size, and count.
5. Download via `/exports/{id}/download` after an owner check; file streamed.

## Implementation map

- `app/Controllers/ExportController.php` — `show`, `create`, `download`.
- `app/Services/ProjectKit.php` — zip assembly, HTML reader, manifest.
- `app/Services/SafeText.php` — escaped-then-formatted rendering.
- `app/Models/Export.php` — `record`, `findForUser`.

## Data used

- **Reads:** approved Artifacts and the Pantry.
- **Writes:** an `exports` row; a zip file on disk in `exports.dir`.

## Dependencies

Every Recipe approved (`approve-and-version`).

## Failure cases

- Not all steps approved → export refused.
- Missing file on disk at download → a fresh build is offered.
- Another user's export id → 404 (never revealed).

## Configuration

`exports.dir` — where zips are written; must sit outside the web root.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`ExportController` + `ProjectKit` traced in full).

## Use caution

Zips live outside the web root and are served only through the authorized
download route; do not move them under `public/`. Content is rendered through
`SafeText` because it originated as pasted (untrusted) input.

## Why it works this way

The kit is the product's finished deliverable — a portable, reviewable artifact
that carries its own provenance (which version, sample or real) with it.

## Related functionality

- Approve & version an artifact
- Track project progress
