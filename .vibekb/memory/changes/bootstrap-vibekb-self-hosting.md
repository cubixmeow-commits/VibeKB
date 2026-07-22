---
id: bootstrap-vibekb-self-hosting
type: change
title: Bootstrap VibeKB on itself with an active self-maintenance lifecycle
summary: Replaced the SousMeow active model with VibeKB's own model, isolated the examples, and added the `vibekb` CLI (status/check/affected) so the model is maintained through a real agent lifecycle as VibeKB is developed.
status: implemented
verification: verified-manually
updated: 2026-07-22
functionality: [start-work-session, detect-drift, find-affected-functionality, load-living-model, generate-static-snapshot]
files: [tools/vibekb.php, guide/index.php, tools/validate.php, .vibekb/manifest.json, CLAUDE.md, AGENTS.md]
session: 2026-07-bootstrap-self-hosting
tags: [self-hosting, workflow, change]
---

## Before

The active `.vibekb/` model described SousMeow (a separate app). VibeKB had a
strong renderer, validator, and explainable-diagram system, but no repository-
resident model of *itself* and no tooling to help an agent run the maintenance
lifecycle or detect drift. Maintenance was prose-only (CLAUDE.md / MAINTENANCE.md).

## After

- The active root `.vibekb/` is VibeKB's own model (this content).
- The SousMeow model and the StopPR field-test audit moved under `examples/`; a
  `VIBEKB_CONTENT_ROOT` override keeps them previewable/validatable.
- A new `tools/vibekb.php` CLI provides `status` (session start), `check` (drift +
  validate + snapshot sync), `affected` (files → functionality), plus `validate`
  and `generate` passthroughs.
- Agent instructions were unified: `CLAUDE.md` is canonical; `AGENTS.md` and
  `.cursor/rules/vibekb.mdc` are thin pointers to the same lifecycle and CLI.
- `tools/validate.php` accepts an optional content-root so examples validate in
  CI; a CI workflow runs syntax, validation, topology, and the drift check.

## Impact

- VibeKB now demonstrates itself: the dynamic guide and `/docs` render VibeKB
  explaining VibeKB.
- A normal code change has a low-friction lifecycle: `status` → work → implement →
  `affected` → update model → `check` → `generate` → handoff.
- Drift between code and model is surfaced mechanically and honestly, without any
  false claim of automatic semantic updates.

## Verification

`php -l` clean across changed PHP; `php tools/validate.php` (root and example) →
0 errors; `php tools/test-topology.php` → OK; `php tools/vibekb.php check` →
consistent; `/docs` regenerated from the self-model and re-validated.
