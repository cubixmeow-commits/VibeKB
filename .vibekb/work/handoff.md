---
id: handoff
type: handoff
title: Current handoff
summary: Homepage hero tagline and self-hosting framing now describe VibeKB (not the old workflow-platform sample). Identity has a one_liner; index.php is in important-files. Next: keep the model reconciled as VibeKB changes; optionally author explainable topology for self-maintenance-loop.
updated: 2026-07-22
verification_state: verified-manually
---

## What the software (VibeKB) now does

VibeKB remains self-hosted: the active `.vibekb/` models VibeKB itself, rendered
through the dynamic guide and static `/docs`. The public homepage now pulls its
hero-card description from VibeKB's identity (`one_liner` / `summary`) and, when
self-hosted, frames the preview as VibeKB explaining itself rather than as a
separate sample product.

## Completed this change

- Added `one_liner` to `.vibekb/project/identity.md`.
- Fixed `index.php` tagline resolution (`one_liner` → `summary` → VibeKB
  fallback) and removed the SousMeow-era hardcoded sentence.
- Adjusted hero-card / carousel / CTA copy for the self-hosted case.
- Recorded `index.php` in important-files; added change memory
  `homepage-tagline-self-hosting`.

## Verification completed

- `php -l index.php` clean.
- Loaded identity via `Content`; confirmed the rendered tagline is the VibeKB
  one_liner and does not contain the old "AI subscriptions" wording.
- `php tools/vibekb.php check` and `php tools/test-topology.php` (run before
  finish); `/docs` regenerated so the overview shows the new one_liner.

## Active warnings (VibeKB)

- `model-can-drift-from-code`, `docs-is-generated-never-hand-edit`,
  `verification-must-reflect-evidence`.

## Honest limitations / not verified

- Homepage was verified by loading identity fields and linting PHP, not by a
  full browser screenshot in this environment.
- Cursor discovery remains `inferred`; live cPanel host not exercised here.

## Exact next recommended action

For any future change, start with `php tools/vibekb.php status`, use `affected`
to find impacted records, update them, and finish with `check` + `generate`
before committing. Optionally author explainable topology for
`self-maintenance-loop` if richer visuals are wanted.
